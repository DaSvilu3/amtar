<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectNoteController extends Controller
{
    /**
     * Store a new note.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'type' => 'required|in:note,comment,reminder',
            'is_pinned' => 'boolean',
            'reminder_date' => 'nullable|date',
            'color' => 'nullable|in:red,yellow,green,blue,purple',
        ]);

        $note = $project->notes()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'type' => $validated['type'],
            'is_pinned' => $validated['is_pinned'] ?? false,
            'reminder_date' => $validated['reminder_date'] ?? null,
            'color' => $validated['color'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'note' => $note->load('user'),
            ]);
        }

        return back()->with('success', 'Note added successfully');
    }

    /**
     * Update a note.
     */
    public function update(Request $request, ProjectNote $note)
    {
        $validated = $request->validate([
            'content' => 'sometimes|required|string|max:5000',
            'is_pinned' => 'boolean',
            'reminder_date' => 'nullable|date',
            'color' => 'nullable|in:red,yellow,green,blue,purple',
        ]);

        $note->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully',
                'note' => $note->fresh()->load('user'),
            ]);
        }

        return back()->with('success', 'Note updated successfully');
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(ProjectNote $note)
    {
        $note->update(['is_pinned' => !$note->is_pinned]);

        return response()->json([
            'success' => true,
            'is_pinned' => $note->is_pinned,
            'message' => $note->is_pinned ? 'Note pinned' : 'Note unpinned',
        ]);
    }

    /**
     * Delete a note.
     */
    public function destroy(ProjectNote $note)
    {
        $note->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
            ]);
        }

        return back()->with('success', 'Note deleted successfully');
    }

    /**
     * Get calendar events for a project.
     */
    public function calendarEvents(Project $project, Request $request)
    {
        $start = $request->get('start', now()->startOfMonth());
        $end = $request->get('end', now()->endOfMonth());

        // Get notes with reminder dates
        $noteEvents = $project->notes()
            ->calendarEvents()
            ->whereBetween('reminder_date', [$start, $end])
            ->with('user')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => 'note-' . $note->id,
                    'title' => substr($note->content, 0, 50) . (strlen($note->content) > 50 ? '...' : ''),
                    'start' => $note->reminder_date->toDateString(),
                    'className' => $note->color_class,
                    'type' => 'note',
                    'extendedProps' => [
                        'content' => $note->content,
                        'user' => $note->user->name,
                        'color' => $note->color,
                        'noteId' => $note->id,
                    ],
                ];
            });

        // Get tasks with due dates
        $taskEvents = $project->tasks()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->with('assignedTo')
            ->get()
            ->map(function ($task) {
                $isOverdue = $task->isOverdue();
                return [
                    'id' => 'task-' . $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->toDateString(),
                    'className' => $isOverdue ? 'bg-danger' : ($task->status === 'completed' ? 'bg-success' : 'bg-primary'),
                    'type' => 'task',
                    'url' => route('admin.tasks.show', $task),
                    'extendedProps' => [
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'assignee' => $task->assignedTo?->name,
                        'isOverdue' => $isOverdue,
                    ],
                ];
            });

        // Get milestones
        $milestoneEvents = $project->milestones()
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [$start, $end])
            ->get()
            ->map(function ($milestone) {
                return [
                    'id' => 'milestone-' . $milestone->id,
                    'title' => '🏁 ' . $milestone->title,
                    'start' => $milestone->target_date->toDateString(),
                    'className' => $milestone->status === 'completed' ? 'bg-success' : 'bg-warning',
                    'type' => 'milestone',
                    'url' => route('admin.milestones.show', $milestone),
                    'extendedProps' => [
                        'status' => $milestone->status,
                    ],
                ];
            });

        return response()->json([
            'events' => $noteEvents->concat($taskEvents)->concat($milestoneEvents)->values(),
        ]);
    }
}
