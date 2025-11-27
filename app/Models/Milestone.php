<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id',
        'service_stage_id',
        'title',
        'description',
        'target_date',
        'completed_at',
        'status',
        'payment_percentage',
        'payment_amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'completed_at' => 'date',
            'payment_percentage' => 'decimal:2',
            'payment_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the project that owns the milestone.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the service stage associated with this milestone.
     */
    public function serviceStage()
    {
        return $this->belongsTo(ServiceStage::class);
    }

    /**
     * Get the tasks associated with this milestone.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Calculate the progress based on completed tasks.
     */
    public function calculateProgress(): int
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Mark the milestone as completed.
     */
    public function markComplete(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        return $this->save();
    }

    /**
     * Check if milestone is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->target_date && $this->target_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Update status based on current conditions.
     */
    public function updateStatus(): void
    {
        if ($this->status === 'completed') {
            return;
        }

        if ($this->isOverdue()) {
            $this->status = 'overdue';
        } elseif ($this->tasks()->whereIn('status', ['in_progress', 'review'])->exists()) {
            $this->status = 'in_progress';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }

    /**
     * Scope for pending milestones.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in-progress milestones.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed milestones.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for overdue milestones.
     */
    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
            ->where('status', '!=', 'completed');
    }
}
