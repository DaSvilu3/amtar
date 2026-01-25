<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use LogsActivity;
    protected $fillable = [
        'project_id',
        'project_service_id',
        'milestone_id',
        'task_template_id',
        'assigned_to',
        'reviewed_by',
        'created_by',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'completed_at',
        'reviewed_at',
        'review_notes',
        'estimated_hours',
        'actual_hours',
        'progress',
        'requires_review',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'date',
            'reviewed_at' => 'datetime',
            'requires_review' => 'boolean',
        ];
    }

    /**
     * Get the project that owns the task.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the project service associated with this task.
     */
    public function projectService()
    {
        return $this->belongsTo(ProjectService::class);
    }

    /**
     * Get the milestone this task belongs to.
     */
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    /**
     * Get the user assigned to this task.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this task.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who reviewed this task.
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the task template this task was created from.
     */
    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    /**
     * Get the files associated with this task.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'entity_id')
            ->where('entity_type', 'Task');
    }

    /**
     * Get the tasks that this task depends on.
     */
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id')
            ->withTimestamps();
    }

    /**
     * Get the tasks that depend on this task.
     */
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id')
            ->withTimestamps();
    }

    /**
     * Check if all dependencies are completed.
     */
    public function canStart(): bool
    {
        return $this->dependencies()
            ->where('status', '!=', 'completed')
            ->doesntExist();
    }

    /**
     * Check if task is blocked by dependencies.
     */
    public function isBlocked(): bool
    {
        return !$this->canStart();
    }

    /**
     * Mark the task as completed.
     */
    public function markComplete(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->progress = 100;
        return $this->save();
    }

    /**
     * Check if task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Scope for pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in-progress tasks.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope for tasks assigned to a specific user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for high priority tasks.
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    /**
     * Scope for tasks requiring review.
     */
    public function scopeRequiresReview($query)
    {
        return $query->where('requires_review', true)
            ->where('status', 'review');
    }

    /**
     * Scope for tasks pending review by a specific user.
     */
    public function scopePendingReviewBy($query, $userId)
    {
        return $query->where('reviewed_by', $userId)
            ->where('status', 'review')
            ->whereNull('reviewed_at');
    }

    /**
     * Submit task for review.
     */
    public function submitForReview(?int $reviewerId = null): bool
    {
        $this->status = 'review';
        if ($reviewerId) {
            $this->reviewed_by = $reviewerId;
        }
        return $this->save();
    }

    /**
     * Approve the task after review.
     */
    public function approveReview(?string $notes = null): bool
    {
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        return $this->markComplete();
    }

    /**
     * Reject the task and send back for revision.
     */
    public function rejectReview(string $notes): bool
    {
        $this->status = 'in_progress';
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        return $this->save();
    }

    /**
     * Check if task needs review before completion.
     */
    public function needsReview(): bool
    {
        return $this->requires_review && $this->status !== 'completed' && !$this->reviewed_at;
    }
}
