<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Milestone;
use App\Models\User;
use App\Models\File;
use App\Services\TaskAssignmentService;
use App\Services\NotificationDispatcher;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TaskReviewSubmittedNotification;
use App\Notifications\TaskApprovedNotification;
use App\Notifications\TaskRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected TaskAssignmentService $assignmentService;

    public function __construct(TaskAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedTo', 'projectService.service', 'milestone']);
        $user = Auth::user();

        // Engineers can only see their own assigned tasks
        if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('reviewed_by', $user->id);
            });
        }

        // Filter by project(s) - support multi-select
        if ($request->filled('project_id')) {
            $projectIds = is_array($request->project_id) ? $request->project_id : [$request->project_id];
            $query->whereIn('project_id', $projectIds);
        }

        // Filter by status(es) - support multi-select
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('status', $statuses);
        }

        // Filter by assigned user(s) - support multi-select
        if ($request->filled('assigned_to')) {
            $assignees = is_array($request->assigned_to) ? $request->assigned_to : [$request->assigned_to];
            if (in_array('unassigned', $assignees)) {
                $query->where(function ($q) use ($assignees) {
                    $q->whereNull('assigned_to');
                    $userIds = array_filter($assignees, fn($a) => $a !== 'unassigned');
                    if (!empty($userIds)) {
                        $q->orWhereIn('assigned_to', $userIds);
                    }
                });
            } else {
                $query->whereIn('assigned_to', $assignees);
            }
        }

        // Filter by priority(ies) - support multi-select
        if ($request->filled('priority')) {
            $priorities = is_array($request->priority) ? $request->priority : [$request->priority];
            $query->whereIn('priority', $priorities);
        }

        // Filter by due date range
        if ($request->filled('due_from')) {
            $query->where('due_date', '>=', $request->due_from);
        }
        if ($request->filled('due_to')) {
            $query->where('due_date', '<=', $request->due_to);
        }

        // Filter overdue only
        if ($request->boolean('overdue_only')) {
            $query->where('due_date', '<', now())
                  ->whereNotIn('status', ['completed', 'cancelled']);
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

        // Calculate statistics for charts
        $statsQuery = Task::query();
        if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            $statsQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('reviewed_by', $user->id);
            });
        }
        if ($request->filled('project_id')) {
            $projectIds = is_array($request->project_id) ? $request->project_id : [$request->project_id];
            $statsQuery->whereIn('project_id', $projectIds);
        }

        $taskStats = [
            'by_status' => $statsQuery->clone()->selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray(),
            'by_priority' => $statsQuery->clone()->selectRaw('priority, COUNT(*) as count')->groupBy('priority')->pluck('count', 'priority')->toArray(),
            'overdue' => $statsQuery->clone()->where('due_date', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'unassigned' => $statsQuery->clone()->whereNull('assigned_to')->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'due_this_week' => $statsQuery->clone()->whereBetween('due_date', [now(), now()->endOfWeek()])->whereNotIn('status', ['completed', 'cancelled'])->count(),
            'urgent_high' => $statsQuery->clone()->whereIn('priority', ['urgent', 'high'])->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        return view('admin.tasks.index', compact('items', 'tasks', 'projects', 'users', 'viewType', 'taskStats'));
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

        // Send notification to assigned user
        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            $assignedUser = User::find($task->assigned_to);
            if ($assignedUser) {
                // In-app notification
                $assignedUser->notify(new TaskAssignedNotification($task->load('project'), Auth::user()->name));
            }

            // Email & WhatsApp notifications via templates
            app(NotificationDispatcher::class)->taskAssigned($task);
        }

        return redirect()->route('admin.tasks.index', ['project_id' => $task->project_id])
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $user = Auth::user();

        // Engineers can only view tasks assigned to them or tasks they review
        if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            if ($task->assigned_to !== $user->id && $task->reviewed_by !== $user->id) {
                abort(403, 'You can only view tasks assigned to you.');
            }
        }

        $task->load([
            'project',
            'projectService.service',
            'milestone',
            'assignedTo',
            'createdBy',
            'dependencies',
            'dependents',
            'files.uploadedBy'
        ]);

        // Get available tasks for dependency management (excluding this task and its dependents to prevent circular dependencies)
        $availableTasks = Task::where('project_id', $task->project_id)
            ->where('id', '!=', $task->id)
            ->whereNotIn('id', $task->dependents->pluck('id'))
            ->get();

        // Build dependency graph data
        $relatedTaskIds = collect()
            ->merge($task->dependencies->pluck('id'))
            ->merge($task->dependents->pluck('id'))
            ->push($task->id)
            ->unique()
            ->values();

        $relatedTasks = Task::whereIn('id', $relatedTaskIds)->get()->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status,
                'priority' => $t->priority,
            ];
        });

        $dependencies = [];
        foreach ($relatedTasks as $relatedTask) {
            $taskModel = Task::find($relatedTask['id']);
            foreach ($taskModel->dependencies as $dep) {
                if ($relatedTaskIds->contains($dep->id)) {
                    $dependencies[] = [
                        'task_id' => $relatedTask['id'],
                        'depends_on_task_id' => $dep->id,
                    ];
                }
            }
        }

        return view('admin.tasks.show', compact('task', 'availableTasks', 'relatedTasks', 'dependencies'));
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

        // Check if assigned_to changed to notify new assignee
        $oldAssignee = $task->assigned_to;
        $newAssignee = $validated['assigned_to'] ?? null;

        $task->update($validated);

        // Notify new assignee if changed
        if ($newAssignee && $newAssignee !== $oldAssignee && $newAssignee !== Auth::id()) {
            $assignedUser = User::find($newAssignee);
            if ($assignedUser) {
                // In-app notification
                $assignedUser->notify(new TaskAssignedNotification($task->load('project'), Auth::user()->name));
            }

            // Email & WhatsApp notifications via templates
            app(NotificationDispatcher::class)->taskAssigned($task);
        }

        // Notify on task completion
        if ($validated['status'] === 'completed' && $task->wasChanged('status')) {
            app(NotificationDispatcher::class)->taskCompleted($task);
        }

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
        $user = Auth::user();

        // Engineers can only update status of their own tasks
        if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            if ($task->assigned_to !== $user->id) {
                abort(403, 'You can only update your own task status.');
            }
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
        ]);

        $wasCompleted = $validated['status'] === 'completed' && $task->status !== 'completed';

        if ($wasCompleted) {
            $task->completed_at = now();
            $task->progress = 100;
        }

        $task->status = $validated['status'];
        $task->save();

        // Send completion notification
        if ($wasCompleted) {
            app(NotificationDispatcher::class)->taskCompleted($task);
        }

        // For non-AJAX requests, redirect back
        if (!$request->expectsJson()) {
            return redirect()->back()->with('success', 'Task status updated.');
        }

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Batch auto-assign multiple tasks
     */
    public function batchAutoAssign(Request $request)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        $tasks = Task::whereIn('id', $validated['task_ids'])
            ->whereNull('assigned_to')
            ->get();

        $results = [];
        foreach ($tasks as $task) {
            $success = $this->assignmentService->autoAssign($task);
            $results[] = [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'assigned_to' => $task->fresh()->assignedTo?->name,
                'success' => $success,
            ];
        }

        $successCount = collect($results)->where('success', true)->count();

        return response()->json([
            'success' => true,
            'message' => "{$successCount} of {$tasks->count()} tasks assigned successfully.",
            'results' => $results,
        ]);
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

    /**
     * Get assignment suggestions for a task.
     */
    public function getAssignmentSuggestions(Task $task)
    {
        $suggestions = $this->assignmentService->getAssignmentSuggestions($task);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions->map(function ($suggestion) {
                return [
                    'user_id' => $suggestion['user']->id,
                    'user_name' => $suggestion['user']->name,
                    'user_email' => $suggestion['user']->email,
                    'score' => round($suggestion['score'], 2),
                    'available_hours' => $suggestion['available_hours'],
                    'current_workload' => $suggestion['current_workload'],
                    'matching_skills' => $suggestion['matching_skills']->pluck('name'),
                ];
            }),
        ]);
    }

    /**
     * Auto-assign a task to the best available consultant.
     */
    public function autoAssign(Task $task)
    {
        $success = $this->assignmentService->autoAssign($task);

        if ($success) {
            $task->load('assignedTo', 'reviewedBy');
            return response()->json([
                'success' => true,
                'message' => 'Task assigned successfully.',
                'task' => $task,
                'assigned_to' => $task->assignedTo?->name,
                'reviewed_by' => $task->reviewedBy?->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No suitable consultant found for this task.',
        ], 422);
    }

    /**
     * Generate tasks from templates for a project service.
     */
    public function generateFromTemplates(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_service_id' => 'required|exists:project_services,id',
            'milestone_id' => 'nullable|exists:milestones,id',
            'auto_assign' => 'boolean',
        ]);

        $projectService = ProjectService::findOrFail($validated['project_service_id']);
        $milestone = isset($validated['milestone_id']) ? Milestone::find($validated['milestone_id']) : null;
        $autoAssign = $validated['auto_assign'] ?? true;

        $tasks = $this->assignmentService->generateTasksFromTemplates(
            $project,
            $projectService,
            $milestone,
            $autoAssign
        );

        return response()->json([
            'success' => true,
            'message' => $tasks->count() . ' tasks generated successfully.',
            'tasks' => $tasks->load('assignedTo', 'reviewedBy'),
        ]);
    }

    /**
     * Submit task for review.
     */
    public function submitForReview(Request $request, Task $task)
    {
        $validated = $request->validate([
            'reviewed_by' => 'nullable|exists:users,id',
        ]);

        // Find a reviewer if not specified
        $reviewerId = $validated['reviewed_by'] ?? null;
        if (!$reviewerId && $task->requires_review) {
            $reviewer = $this->assignmentService->findBestReviewer($task);
            $reviewerId = $reviewer?->id;
        }

        $task->submitForReview($reviewerId);

        // Notify the reviewer
        if ($reviewerId) {
            $reviewer = User::find($reviewerId);
            if ($reviewer && $reviewer->id !== Auth::id()) {
                $reviewer->notify(new TaskReviewSubmittedNotification($task->load('project'), Auth::user()->name));
            }
        }

        // Also notify project manager if different from reviewer
        $project = $task->project;
        if ($project && $project->project_manager_id && $project->project_manager_id !== $reviewerId && $project->project_manager_id !== Auth::id()) {
            $pm = User::find($project->project_manager_id);
            if ($pm) {
                $pm->notify(new TaskReviewSubmittedNotification($task->load('project'), Auth::user()->name));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task submitted for review.',
            'task' => $task->load('reviewedBy'),
        ]);
    }

    /**
     * Approve a task after review.
     */
    public function approveReview(Request $request, Task $task)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $task->approveReview($validated['notes'] ?? null);

        // Notify the assignee that their task was approved
        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskApprovedNotification($task->load('project'), Auth::user()->name, $validated['notes'] ?? null));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task approved and completed.',
            'task' => $task,
        ]);
    }

    /**
     * Reject a task and send back for revision.
     */
    public function rejectReview(Request $request, Task $task)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $task->rejectReview($validated['notes']);

        // Notify the assignee that their task was rejected
        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            $assignee = User::find($task->assigned_to);
            if ($assignee) {
                $assignee->notify(new TaskRejectedNotification($task->load('project'), Auth::user()->name, $validated['notes']));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task sent back for revision.',
            'task' => $task,
        ]);
    }

    /**
     * Get tasks pending review for current user.
     */
    public function pendingReviews(Request $request)
    {
        $tasks = Task::with(['project', 'assignedTo', 'projectService.service'])
            ->pendingReviewBy(Auth::id())
            ->orderBy('due_date')
            ->paginate(20);

        return view('admin.tasks.pending-reviews', compact('tasks'));
    }

    /**
     * Update task progress (for assigned user).
     */
    public function updateProgress(Request $request, Task $task)
    {
        $user = Auth::user();

        // Allow if: assigned to task, is PM, or is admin
        if ($task->assigned_to !== $user->id && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this task.',
            ], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $task->update([
            'progress' => $validated['progress'],
            'actual_hours' => $validated['actual_hours'] ?? $task->actual_hours,
        ]);

        // If progress is 100%, auto-complete if no review required
        if ($validated['progress'] == 100 && !$task->requires_review && $task->status !== 'completed') {
            // Don't auto-complete, let user explicitly complete
        }

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully.',
            'task' => $task,
        ]);
    }

    /**
     * Upload a file to a task.
     */
    public function uploadFile(Request $request, Task $task)
    {
        $user = Auth::user();

        // Allow if: assigned to task, is PM, or is admin
        if ($task->assigned_to !== $user->id && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to upload files to this task.',
            ], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();

        $filePath = $uploadedFile->store('tasks/' . $task->id, 'public');

        $file = File::create([
            'name' => $validated['name'] ?? pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'category' => 'task_document',
            'description' => $validated['description'] ?? null,
            'uploaded_by' => Auth::id(),
            'entity_type' => 'Task',
            'entity_id' => $task->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'file' => $file,
            ]);
        }

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Delete a file from a task.
     */
    public function deleteFile(Request $request, Task $task, File $file)
    {
        $user = Auth::user();

        // Verify file belongs to this task
        if ($file->entity_type !== 'Task' || $file->entity_id !== $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'File does not belong to this task.',
            ], 403);
        }

        // Allow if: file uploader, task assignee, is PM, or is admin
        if ($file->uploaded_by !== $user->id && $task->assigned_to !== $user->id && !$user->hasAnyRole(['administrator', 'project-manager'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this file.',
            ], 403);
        }

        // Delete file from storage
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'File deleted successfully.');
    }

    /**
     * Add a dependency to a task.
     */
    public function addDependency(Request $request, Task $task)
    {
        $validated = $request->validate([
            'depends_on_task_id' => 'required|exists:tasks,id',
            'dependency_type' => 'nullable|in:finish_to_start,start_to_start,finish_to_finish',
        ]);

        $dependsOnTaskId = $validated['depends_on_task_id'];

        // Prevent self-dependency
        if ($task->id === $dependsOnTaskId) {
            return response()->json([
                'success' => false,
                'message' => 'A task cannot depend on itself.',
            ], 422);
        }

        // Prevent circular dependencies
        $dependsOnTask = Task::find($dependsOnTaskId);
        if ($dependsOnTask && $this->wouldCreateCircularDependency($task, $dependsOnTask)) {
            return response()->json([
                'success' => false,
                'message' => 'This dependency would create a circular reference.',
            ], 422);
        }

        // Check if dependency already exists
        if ($task->dependencies()->where('depends_on_task_id', $dependsOnTaskId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This dependency already exists.',
            ], 422);
        }

        // Add dependency
        $task->dependencies()->attach($dependsOnTaskId, [
            'dependency_type' => $validated['dependency_type'] ?? 'finish_to_start',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dependency added successfully.',
            'dependency' => $dependsOnTask,
        ]);
    }

    /**
     * Remove a dependency from a task.
     */
    public function removeDependency(Task $task, Task $dependency)
    {
        // Check if dependency exists
        if (!$task->dependencies()->where('depends_on_task_id', $dependency->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This dependency does not exist.',
            ], 404);
        }

        // Remove dependency
        $task->dependencies()->detach($dependency->id);

        return response()->json([
            'success' => true,
            'message' => 'Dependency removed successfully.',
        ]);
    }

    /**
     * Check if adding a dependency would create a circular reference.
     */
    private function wouldCreateCircularDependency(Task $task, Task $dependsOnTask): bool
    {
        // If the task we're depending on depends on us (directly or indirectly), it's circular
        return $this->taskDependsOn($dependsOnTask, $task->id);
    }

    /**
     * Recursively check if a task depends on another task.
     */
    private function taskDependsOn(Task $task, int $targetTaskId, array &$visited = []): bool
    {
        // Prevent infinite loops
        if (in_array($task->id, $visited)) {
            return false;
        }

        $visited[] = $task->id;

        // Check direct dependencies
        foreach ($task->dependencies as $dependency) {
            if ($dependency->id === $targetTaskId) {
                return true;
            }

            // Check recursive dependencies
            if ($this->taskDependsOn($dependency, $targetTaskId, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get dependency graph data for visualization.
     */
    public function getDependencyGraph(Task $task)
    {
        // Get all related tasks (dependencies and dependents)
        $relatedTaskIds = collect()
            ->merge($task->dependencies->pluck('id'))
            ->merge($task->dependents->pluck('id'))
            ->push($task->id)
            ->unique()
            ->values();

        // Get all tasks with their dependencies
        $relatedTasks = Task::whereIn('id', $relatedTaskIds)
            ->with('dependencies')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'status' => $t->status,
                    'priority' => $t->priority,
                ];
            });

        // Build dependency edges
        $dependencies = [];
        foreach ($relatedTasks as $relatedTask) {
            $taskModel = Task::find($relatedTask['id']);
            foreach ($taskModel->dependencies as $dep) {
                if ($relatedTaskIds->contains($dep->id)) {
                    $dependencies[] = [
                        'task_id' => $relatedTask['id'],
                        'depends_on_task_id' => $dep->id,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'tasks' => $relatedTasks,
            'dependencies' => $dependencies,
        ]);
    }
}
