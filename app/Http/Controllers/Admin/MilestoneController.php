<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ServiceStage;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request)
    {
        $query = Milestone::with(['project', 'serviceStage', 'tasks']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('target_date')->paginate(20);
        $projects = Project::orderBy('name')->get();

        return view('admin.milestones.index', compact('items', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = Project::with(['services.serviceStage'])->orderBy('name')->get();
        $serviceStages = ServiceStage::orderBy('sort_order')->get();

        // Pre-select project if provided
        $selectedProject = $request->filled('project_id')
            ? Project::with(['services.serviceStage'])->find($request->project_id)
            : null;

        return view('admin.milestones.create', compact('projects', 'serviceStages', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_stage_id' => 'nullable|exists:service_stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,overdue',
            'payment_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        Milestone::create($validated);

        return redirect()->route('admin.milestones.index', ['project_id' => $validated['project_id']])
            ->with('success', 'Milestone created successfully.');
    }

    public function show(Milestone $milestone)
    {
        $milestone->load(['project', 'serviceStage', 'tasks.assignedTo']);

        return view('admin.milestones.show', compact('milestone'));
    }

    public function edit(Milestone $milestone)
    {
        $projects = Project::with(['services.serviceStage'])->orderBy('name')->get();
        $serviceStages = ServiceStage::orderBy('sort_order')->get();

        return view('admin.milestones.edit', compact('milestone', 'projects', 'serviceStages'));
    }

    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'service_stage_id' => 'nullable|exists:service_stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,overdue',
            'payment_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);

        // Handle completion
        if ($validated['status'] === 'completed' && $milestone->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $milestone->update($validated);

        return redirect()->route('admin.milestones.index', ['project_id' => $milestone->project_id])
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(Milestone $milestone)
    {
        $projectId = $milestone->project_id;
        $milestone->delete();

        return redirect()->route('admin.milestones.index', ['project_id' => $projectId])
            ->with('success', 'Milestone deleted successfully.');
    }

    /**
     * Generate milestones from project's service stages
     */
    public function generateFromProject(Request $request, Project $project)
    {
        // Get unique service stages from project services
        $stageIds = $project->services()
            ->distinct()
            ->pluck('service_stage_id')
            ->filter();

        $stages = ServiceStage::whereIn('id', $stageIds)
            ->orderBy('sort_order')
            ->get();

        $sortOrder = $project->milestones()->max('sort_order') ?? 0;

        foreach ($stages as $stage) {
            // Check if milestone already exists for this stage
            $exists = $project->milestones()
                ->where('service_stage_id', $stage->id)
                ->exists();

            if (!$exists) {
                Milestone::create([
                    'project_id' => $project->id,
                    'service_stage_id' => $stage->id,
                    'title' => $stage->name,
                    'description' => $stage->description,
                    'status' => 'pending',
                    'sort_order' => ++$sortOrder,
                ]);
            }
        }

        return redirect()->route('admin.milestones.index', ['project_id' => $project->id])
            ->with('success', 'Milestones generated from project service stages.');
    }

    /**
     * Get milestones for a specific project (API endpoint)
     */
    public function getProjectMilestones(Project $project)
    {
        $milestones = $project->milestones()
            ->with(['serviceStage', 'tasks'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($milestone) {
                $milestone->progress = $milestone->calculateProgress();
                return $milestone;
            });

        return response()->json($milestones);
    }
}
