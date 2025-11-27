<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedTo', 'projectService.service', 'milestone']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Get items based on view type
        $viewType = $request->get('view', 'list');

        if ($viewType === 'kanban') {
            $tasks = [
                'pending' => $query->clone()->where('status', 'pending')->orderBy('sort_order')->get(),
                'in_progress' => $query->clone()->where('status', 'in_progress')->orderBy('sort_order')->get(),
                'review' => $query->clone()->where('status', 'review')->orderBy('sort_order')->get(),
                'completed' => $query->clone()->where('status', 'completed')->orderBy('sort_order')->get(),
            ];
            $items = null;
        } else {
            $items = $query->orderBy('due_date')->orderBy('priority', 'desc')->paginate(20);
            $tasks = null;
        }

        $projects = Project::orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.index', compact('items', 'tasks', 'projects', 'users', 'viewType'));
    }

    public function create(Request $request)
    {
        $projects = Project::with(['services.service', 'milestones'])->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        // Pre-select project if provided
        $selectedProject = $request->filled('project_id')
            ? Project::with(['services.service', 'milestones'])->find($request->project_id)
            : null;

        return view('admin.tasks.create', compact('projects', 'users', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_service_id' => 'nullable|exists:project_services,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:tasks,id',
        ]);

        $validated['created_by'] = Auth::id();

        $task = Task::create($validated);

        // Attach dependencies
        if ($request->filled('dependencies')) {
            $task->dependencies()->attach($request->dependencies);
        }

        return redirect()->route('admin.tasks.index', ['project_id' => $task->project_id])
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load([
            'project',
            'projectService.service',
            'milestone',
            'assignedTo',
            'createdBy',
            'dependencies',
            'dependents'
        ]);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $task->load(['dependencies']);
        $projects = Project::with(['services.service', 'milestones'])->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        // Get available tasks for dependencies (excluding this task and its dependents)
        $availableTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->whereNotIn('id', $task->dependents->pluck('id'))
            ->get();

        return view('admin.tasks.edit', compact('task', 'projects', 'users', 'availableTasks'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_service_id' => 'nullable|exists:project_services,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'assigned_to' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'progress' => 'nullable|integer|min:0|max:100',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:tasks,id',
        ]);

        // Handle completion
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $task->update($validated);

        // Sync dependencies
        $task->dependencies()->sync($request->input('dependencies', []));

        return redirect()->route('admin.tasks.index', ['project_id' => $task->project_id])
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->route('admin.tasks.index', ['project_id' => $projectId])
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Quick status update (for kanban board)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $task->completed_at = now();
            $task->progress = 100;
        }

        $task->status = $validated['status'];
        $task->save();

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Reorder tasks (for drag and drop)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])->update(['sort_order' => $taskData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get tasks for a specific project (API endpoint)
     */
    public function getProjectTasks(Project $project)
    {
        $tasks = $project->tasks()
            ->with(['assignedTo', 'projectService.service'])
            ->orderBy('sort_order')
            ->get();

        return response()->json($tasks);
    }
}
