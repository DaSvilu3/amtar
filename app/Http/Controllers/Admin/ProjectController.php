<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectService;
use App\Models\File;
use App\Models\DocumentType;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\ServicePackage;
use App\Models\Service;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $items = Project::with('client', 'projectManager')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.projects.index', compact('items'));
    }

    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $mainServices = MainService::where('is_active', true)->orderBy('sort_order')->get();

        // Use wizard view if enabled in config
        $viewName = config('project.creation.use_wizard', true)
            ? 'admin.projects.create-wizard'
            : 'admin.projects.create';

        return view($viewName, compact('clients', 'users', 'mainServices'));
    }

    public function store(Request $request)
    {
        // Build dynamic validation rules based on config
        $rules = $this->getValidationRules($request);
        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $project = Project::create($validated);

            // Handle services - package services + custom services + section services
            $this->handleProjectServices($project, $request);

            // Handle document uploads
            if ($request->has('documents') && !empty($request->file('documents'))) {
                $this->handleDocumentUploads($request->file('documents'), $project);
            }

            // Generate contract if requested or auto-enabled
            $shouldGenerateContract = $this->shouldGenerateContract($request);
            if ($shouldGenerateContract) {
                $this->generateContract($project, $request);
                $message = 'Project and contract created successfully.';
            } else {
                $message = 'Project created successfully.';
            }

            DB::commit();
            return redirect()->route('admin.projects.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create project: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'projectManager',
            'mainService',
            'subService',
            'servicePackage',
            'services.service.serviceStage',
            'contracts',
            'files.documentType',
            'tasks.assignedTo',
            'tasks.projectService.service',
            'milestones.serviceStage',
            'milestones.tasks'
        ]);

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        $mainServices = MainService::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.projects.edit', compact('project', 'clients', 'users', 'mainServices'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_number' => 'required|string|max:255|unique:projects,project_number,' . $project->id,
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'status' => 'required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'main_service_id' => 'required|exists:main_services,id',
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'service_package_id' => 'nullable|exists:service_packages,id',
            'location' => 'nullable|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'custom_services' => 'nullable|array',
            'custom_services.*' => 'exists:services,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $project->update($validated);

            // Handle services - package services + custom services
            $this->handleProjectServices($project, $request);

            // Handle document uploads
            if ($request->has('documents')) {
                $this->handleDocumentUploads($request->file('documents'), $project);
            }

            DB::commit();
            return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update project: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    /**
     * Handle project services from package, custom services, and section selection
     */
    private function handleProjectServices(Project $project, Request $request)
    {
        // Clear existing services only if not using editable package mode
        $editablePackageServices = config('project.creation.editable_package_services', true);

        if (!$editablePackageServices || !$request->has('keep_existing_services')) {
            ProjectService::where('project_id', $project->id)->delete();
        }

        $sortOrder = 1;
        $allServices = [];

        // Add services from selected package
        if ($request->service_package_id) {
            $package = ServicePackage::with('services')->find($request->service_package_id);
            if ($package) {
                foreach ($package->services as $service) {
                    // Check if package services are editable and service is removed
                    $isRemoved = $editablePackageServices
                        && $request->has('removed_package_services')
                        && in_array($service->id, $request->removed_package_services);

                    if (!$isRemoved) {
                        $allServices[$service->id] = [
                            'is_from_package' => true,
                            'service_stage_id' => $service->service_stage_id,
                            'sort_order' => $sortOrder++,
                        ];
                    }
                }
            }
        }

        // Add services from selected sections (if section selection is enabled)
        if (config('project.creation.enable_section_selection', true)
            && $request->has('selected_sections')
            && is_array($request->selected_sections)) {

            foreach ($request->selected_sections as $stageId) {
                $sectionServices = Service::where('service_stage_id', $stageId)->get();
                foreach ($sectionServices as $service) {
                    if (!isset($allServices[$service->id])) {
                        $allServices[$service->id] = [
                            'is_from_package' => false,
                            'service_stage_id' => $service->service_stage_id,
                            'sort_order' => $sortOrder++,
                        ];
                    }
                }
            }
        }

        // Add custom services (not already in package or sections)
        if ($request->has('custom_services') && is_array($request->custom_services)) {
            foreach ($request->custom_services as $serviceId) {
                if (!isset($allServices[$serviceId])) {
                    $service = Service::find($serviceId);
                    if ($service) {
                        $allServices[$serviceId] = [
                            'is_from_package' => false,
                            'service_stage_id' => $service->service_stage_id,
                            'sort_order' => $sortOrder++,
                        ];
                    }
                }
            }
        }

        // Create project service records
        foreach ($allServices as $serviceId => $data) {
            ProjectService::create([
                'project_id' => $project->id,
                'service_id' => $serviceId,
                'service_stage_id' => $data['service_stage_id'],
                'is_from_package' => $data['is_from_package'],
                'is_completed' => false,
                'sort_order' => $data['sort_order'],
            ]);
        }
    }

    /**
     * Generate contract automatically for the project
     */
    private function generateContract(Project $project, Request $request)
    {
        // Get all services for the contract
        $services = ProjectService::with('service', 'serviceStage')
            ->where('project_id', $project->id)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('serviceStage.name')
            ->map(function ($stageServices) {
                return $stageServices->pluck('service.name')->toArray();
            })
            ->toArray();

        // Generate contract number
        $contractNumber = 'CNT-' . date('Y') . '-' . str_pad($project->id, 4, '0', STR_PAD_LEFT);

        // Get main service and package names for contract title
        $mainService = MainService::find($project->main_service_id);
        $subService = $project->sub_service_id ? SubService::find($project->sub_service_id) : null;
        $package = $project->service_package_id ? ServicePackage::find($project->service_package_id) : null;

        $serviceDescription = $mainService->name;
        if ($subService) {
            $serviceDescription .= ' - ' . $subService->name;
        }
        if ($package) {
            $serviceDescription .= ' (' . $package->name . ')';
        }

        // Create contract
        Contract::create([
            'contract_number' => $contractNumber,
            'title' => $project->name . ' - ' . $serviceDescription,
            'client_id' => $project->client_id,
            'project_id' => $project->id,
            'description' => $project->description,
            'value' => $project->budget,
            'currency' => 'OMR',
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'status' => 'draft',
            'services' => $services,
            'auto_generated' => true,
            'terms' => 'This contract is automatically generated based on the project services. Please review and update the terms as necessary.',
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Handle document uploads for a project.
     */
    private function handleDocumentUploads($documents, Project $project)
    {
        foreach ($documents as $documentSlug => $file) {
            if ($file && $file->isValid()) {
                // Find the document type
                $documentType = DocumentType::where('slug', $documentSlug)
                    ->where('entity_type', 'project')
                    ->first();

                if ($documentType) {
                    // Store the file
                    $path = $file->store('documents/projects/' . $project->id, 'public');
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();

                    // Create file record
                    File::create([
                        'name' => $documentType->name,
                        'original_name' => $originalName,
                        'file_path' => $path,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'category' => 'documents',
                        'description' => 'Project document: ' . $documentType->name,
                        'uploaded_by' => Auth::id(),
                        'is_public' => false,
                        'document_type_id' => $documentType->id,
                        'entity_type' => 'project',
                        'entity_id' => $project->id,
                    ]);
                }
            }
        }
    }

    /**
     * API endpoint to get sub-services for a main service
     */
    public function getSubServices($mainServiceId)
    {
        $subServices = SubService::where('main_service_id', $mainServiceId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json($subServices);
    }

    /**
     * API endpoint to get packages for a service (main or sub)
     */
    public function getPackages(Request $request)
    {
        $query = ServicePackage::where('is_active', true);

        if ($request->has('sub_service_id') && $request->sub_service_id) {
            $query->where('sub_service_id', $request->sub_service_id);
        } elseif ($request->has('main_service_id') && $request->main_service_id) {
            $query->where('main_service_id', $request->main_service_id)
                  ->whereNull('sub_service_id');
        }

        $packages = $query->orderBy('sort_order')->get(['id', 'name', 'description', 'slug']);

        return response()->json($packages);
    }

    /**
     * API endpoint to get services in a package
     */
    public function getPackageServices($packageId)
    {
        $package = ServicePackage::with(['services.serviceStage'])
            ->find($packageId);

        if (!$package) {
            return response()->json([]);
        }

        $services = $package->services->groupBy('serviceStage.name')->map(function ($stageServices) {
            return $stageServices->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'is_optional' => $service->is_optional,
                ];
            });
        });

        return response()->json($services);
    }

    /**
     * API endpoint to get all available services for custom selection
     */
    public function getAllServices()
    {
        $services = Service::with('serviceStage')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('serviceStage.name')
            ->map(function ($stageServices) {
                return $stageServices->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'slug' => $service->slug,
                        'is_optional' => $service->is_optional,
                    ];
                });
            });

        return response()->json($services);
    }

    /**
     * API endpoint to get all service stages for section selection
     */
    public function getServiceStages()
    {
        $stages = \App\Models\ServiceStage::with(['services' => function($query) {
            $query->orderBy('sort_order');
        }])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($stage) {
                return [
                    'id' => $stage->id,
                    'name' => $stage->name,
                    'slug' => $stage->slug,
                    'description' => $stage->description,
                    'service_count' => $stage->services->count(),
                    'services' => $stage->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                        ];
                    }),
                ];
            });

        return response()->json($stages);
    }

    /**
     * Determine if contract should be generated
     */
    private function shouldGenerateContract(Request $request): bool
    {
        // Check if auto-generation is forced
        if (config('project.creation.auto_generate_contract', false)) {
            return true;
        }

        // Check user's choice (checkbox in form)
        if ($request->has('generate_contract')) {
            return $request->boolean('generate_contract');
        }

        // Use default setting
        return config('project.creation.generate_contract_by_default', true);
    }

    /**
     * Get validation rules based on config and request
     */
    private function getValidationRules(Request $request): array
    {
        $documentConfig = config('project.documents', []);
        $maxFileSize = $documentConfig['max_file_size'] ?? 10240;
        $allowedTypes = implode(',', $documentConfig['allowed_types'] ?? ['pdf', 'jpg', 'jpeg', 'png']);

        $rules = [
            'name' => 'required|string|max:255',
            'project_number' => 'required|string|max:255|unique:projects,project_number',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'status' => 'required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'main_service_id' => 'required|exists:main_services,id',
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'location' => 'nullable|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'custom_services' => 'nullable|array',
            'custom_services.*' => 'exists:services,id',
            'selected_sections' => 'nullable|array',
            'selected_sections.*' => 'exists:service_stages,id',
            'removed_package_services' => 'nullable|array',
            'removed_package_services.*' => 'exists:services,id',
            'generate_contract' => 'nullable|boolean',
            'documents' => 'nullable|array',
            'documents.*' => "nullable|file|mimes:{$allowedTypes}|max:{$maxFileSize}",
        ];

        // Make package optional based on config
        if (config('project.creation.require_package', false)) {
            $rules['service_package_id'] = 'required|exists:service_packages,id';
        } else {
            $rules['service_package_id'] = 'nullable|exists:service_packages,id';
        }

        return $rules;
    }
}
