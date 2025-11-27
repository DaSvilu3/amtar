<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainService;
use App\Models\SubService;
use App\Models\ServicePackage;
use App\Models\ServiceStage;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceManagementController extends Controller
{
    // ==================== MAIN SERVICES ====================

    public function mainServicesIndex()
    {
        $mainServices = MainService::withCount(['subServices', 'servicePackages', 'projects'])
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.services.main-services.index', compact('mainServices'));
    }

    public function mainServicesCreate()
    {
        return view('admin.services.main-services.create');
    }

    public function mainServicesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        MainService::create($validated);

        return redirect()->route('admin.services.main.index')
            ->with('success', 'Main service created successfully.');
    }

    public function mainServicesEdit(MainService $mainService)
    {
        return view('admin.services.main-services.edit', compact('mainService'));
    }

    public function mainServicesUpdate(Request $request, MainService $mainService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $mainService->update($validated);

        return redirect()->route('admin.services.main.index')
            ->with('success', 'Main service updated successfully.');
    }

    public function mainServicesDestroy(MainService $mainService)
    {
        if ($mainService->projects()->exists()) {
            return redirect()->route('admin.services.main.index')
                ->with('error', 'Cannot delete main service with existing projects.');
        }

        $mainService->delete();

        return redirect()->route('admin.services.main.index')
            ->with('success', 'Main service deleted successfully.');
    }

    // ==================== SUB SERVICES ====================

    public function subServicesIndex(Request $request)
    {
        $query = SubService::with('mainService')
            ->withCount(['servicePackages', 'projects']);

        if ($request->filled('main_service_id')) {
            $query->where('main_service_id', $request->main_service_id);
        }

        $subServices = $query->orderBy('sort_order')->paginate(20);
        $mainServices = MainService::orderBy('name')->get();

        return view('admin.services.sub-services.index', compact('subServices', 'mainServices'));
    }

    public function subServicesCreate()
    {
        $mainServices = MainService::where('is_active', true)->orderBy('name')->get();
        return view('admin.services.sub-services.create', compact('mainServices'));
    }

    public function subServicesStore(Request $request)
    {
        $validated = $request->validate([
            'main_service_id' => 'required|exists:main_services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        SubService::create($validated);

        return redirect()->route('admin.services.sub.index')
            ->with('success', 'Sub service created successfully.');
    }

    public function subServicesEdit(SubService $subService)
    {
        $mainServices = MainService::where('is_active', true)->orderBy('name')->get();
        return view('admin.services.sub-services.edit', compact('subService', 'mainServices'));
    }

    public function subServicesUpdate(Request $request, SubService $subService)
    {
        $validated = $request->validate([
            'main_service_id' => 'required|exists:main_services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $subService->update($validated);

        return redirect()->route('admin.services.sub.index')
            ->with('success', 'Sub service updated successfully.');
    }

    public function subServicesDestroy(SubService $subService)
    {
        if ($subService->projects()->exists()) {
            return redirect()->route('admin.services.sub.index')
                ->with('error', 'Cannot delete sub service with existing projects.');
        }

        $subService->delete();

        return redirect()->route('admin.services.sub.index')
            ->with('success', 'Sub service deleted successfully.');
    }

    // ==================== SERVICE PACKAGES ====================

    public function packagesIndex(Request $request)
    {
        $query = ServicePackage::with(['mainService', 'subService'])
            ->withCount(['services', 'projects']);

        if ($request->filled('main_service_id')) {
            $query->where('main_service_id', $request->main_service_id);
        }

        if ($request->filled('sub_service_id')) {
            $query->where('sub_service_id', $request->sub_service_id);
        }

        $packages = $query->orderBy('sort_order')->paginate(20);
        $mainServices = MainService::orderBy('name')->get();
        $subServices = SubService::orderBy('name')->get();

        return view('admin.services.packages.index', compact('packages', 'mainServices', 'subServices'));
    }

    public function packagesCreate()
    {
        $mainServices = MainService::where('is_active', true)->orderBy('name')->get();
        $subServices = SubService::where('is_active', true)->orderBy('name')->get();
        $stages = ServiceStage::orderBy('sort_order')->get();
        $services = Service::with('serviceStage')->orderBy('sort_order')->get();

        return view('admin.services.packages.create', compact('mainServices', 'subServices', 'stages', 'services'));
    }

    public function packagesStore(Request $request)
    {
        $validated = $request->validate([
            'main_service_id' => 'required|exists:main_services,id',
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $package = ServicePackage::create($validated);

        // Attach services to package
        if (!empty($request->services)) {
            $servicesData = [];
            foreach ($request->services as $index => $serviceId) {
                $service = Service::find($serviceId);
                $servicesData[$serviceId] = [
                    'service_stage_id' => $service->service_stage_id,
                    'sort_order' => $index,
                ];
            }
            $package->services()->attach($servicesData);
        }

        return redirect()->route('admin.services.packages.index')
            ->with('success', 'Service package created successfully.');
    }

    public function packagesShow(ServicePackage $package)
    {
        $package->load(['mainService', 'subService', 'services.serviceStage']);
        return view('admin.services.packages.show', compact('package'));
    }

    public function packagesEdit(ServicePackage $package)
    {
        $mainServices = MainService::where('is_active', true)->orderBy('name')->get();
        $subServices = SubService::where('is_active', true)->orderBy('name')->get();
        $stages = ServiceStage::orderBy('sort_order')->get();
        $services = Service::with('serviceStage')->orderBy('sort_order')->get();
        $package->load('services');

        return view('admin.services.packages.edit', compact('package', 'mainServices', 'subServices', 'stages', 'services'));
    }

    public function packagesUpdate(Request $request, ServicePackage $package)
    {
        $validated = $request->validate([
            'main_service_id' => 'required|exists:main_services,id',
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $package->update($validated);

        // Sync services
        if (!empty($request->services)) {
            $servicesData = [];
            foreach ($request->services as $index => $serviceId) {
                $service = Service::find($serviceId);
                $servicesData[$serviceId] = [
                    'service_stage_id' => $service->service_stage_id,
                    'sort_order' => $index,
                ];
            }
            $package->services()->sync($servicesData);
        } else {
            $package->services()->detach();
        }

        return redirect()->route('admin.services.packages.index')
            ->with('success', 'Service package updated successfully.');
    }

    public function packagesDestroy(ServicePackage $package)
    {
        if ($package->projects()->exists()) {
            return redirect()->route('admin.services.packages.index')
                ->with('error', 'Cannot delete package with existing projects.');
        }

        $package->services()->detach();
        $package->delete();

        return redirect()->route('admin.services.packages.index')
            ->with('success', 'Service package deleted successfully.');
    }

    // ==================== SERVICE STAGES ====================

    public function stagesIndex()
    {
        $stages = ServiceStage::withCount('services')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.services.stages.index', compact('stages'));
    }

    public function stagesCreate()
    {
        return view('admin.services.stages.create');
    }

    public function stagesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        ServiceStage::create($validated);

        return redirect()->route('admin.services.stages.index')
            ->with('success', 'Service stage created successfully.');
    }

    public function stagesEdit(ServiceStage $stage)
    {
        return view('admin.services.stages.edit', compact('stage'));
    }

    public function stagesUpdate(Request $request, ServiceStage $stage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $stage->update($validated);

        return redirect()->route('admin.services.stages.index')
            ->with('success', 'Service stage updated successfully.');
    }

    public function stagesDestroy(ServiceStage $stage)
    {
        if ($stage->services()->exists()) {
            return redirect()->route('admin.services.stages.index')
                ->with('error', 'Cannot delete stage with existing services.');
        }

        $stage->delete();

        return redirect()->route('admin.services.stages.index')
            ->with('success', 'Service stage deleted successfully.');
    }

    // ==================== SERVICES (Individual) ====================

    public function servicesIndex(Request $request)
    {
        $query = Service::with('serviceStage');

        if ($request->filled('stage_id')) {
            $query->where('service_stage_id', $request->stage_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->orderBy('service_stage_id')
            ->orderBy('sort_order')
            ->paginate(30);

        $stages = ServiceStage::orderBy('sort_order')->get();

        return view('admin.services.services.index', compact('services', 'stages'));
    }

    public function servicesCreate()
    {
        $stages = ServiceStage::orderBy('sort_order')->get();
        return view('admin.services.services.create', compact('stages'));
    }

    public function servicesStore(Request $request)
    {
        $validated = $request->validate([
            'service_stage_id' => 'required|exists:service_stages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_optional' => 'boolean',
            'required_documents' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_optional'] = $request->has('is_optional');

        Service::create($validated);

        return redirect()->route('admin.services.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function servicesEdit(Service $service)
    {
        $stages = ServiceStage::orderBy('sort_order')->get();
        return view('admin.services.services.edit', compact('service', 'stages'));
    }

    public function servicesUpdate(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_stage_id' => 'required|exists:service_stages,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_optional' => 'boolean',
            'required_documents' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_optional'] = $request->has('is_optional');

        $service->update($validated);

        return redirect()->route('admin.services.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function servicesDestroy(Service $service)
    {
        if ($service->projectServices()->exists()) {
            return redirect()->route('admin.services.services.index')
                ->with('error', 'Cannot delete service that is used in projects.');
        }

        $service->servicePackages()->detach();
        $service->delete();

        return redirect()->route('admin.services.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    // ==================== DASHBOARD/OVERVIEW ====================

    public function index()
    {
        $stats = [
            'main_services' => MainService::count(),
            'sub_services' => SubService::count(),
            'packages' => ServicePackage::count(),
            'stages' => ServiceStage::count(),
            'services' => Service::count(),
        ];

        $recentPackages = ServicePackage::with(['mainService', 'subService'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $mainServices = MainService::with('subServices')
            ->withCount(['subServices', 'servicePackages'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.services.index', compact('stats', 'recentPackages', 'mainServices'));
    }
}
