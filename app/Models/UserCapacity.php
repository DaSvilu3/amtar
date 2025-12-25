<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserCapacity extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'total_hours',
        'allocated_hours',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
        ];
    }

    /**
     * Get the user this capacity belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get available hours for this week.
     */
    public function getAvailableHoursAttribute(): int
    {
        return max(0, $this->total_hours - $this->allocated_hours);
    }

    /**
     * Get utilization percentage.
     */
    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->total_hours === 0) {
            return 0;
        }
        return round(($this->allocated_hours / $this->total_hours) * 100, 1);
    }

    /**
     * Check if user is over-allocated.
     */
    public function isOverAllocated(): bool
    {
        return $this->allocated_hours > $this->total_hours;
    }

    /**
     * Allocate hours to this week.
     */
    public function allocate(int $hours): bool
    {
        $this->allocated_hours += $hours;
        return $this->save();
    }

    /**
     * Deallocate hours from this week.
     */
    public function deallocate(int $hours): bool
    {
        $this->allocated_hours = max(0, $this->allocated_hours - $hours);
        return $this->save();
    }

    /**
     * Get or create capacity for a user and week.
     */
    public static function getOrCreateForWeek(int $userId, ?Carbon $date = null): self
    {
        $date = $date ?? now();
        $weekStart = $date->startOfWeek();

        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'week_start' => $weekStart,
            ],
            [
                'total_hours' => 40,
                'allocated_hours' => 0,
            ]
        );
    }

    /**
     * Scope for a specific week.
     */
    public function scopeForWeek($query, Carbon $date)
    {
        return $query->where('week_start', $date->startOfWeek());
    }

    /**
     * Scope for users with availability.
     */
    public function scopeWithAvailability($query, int $minHours = 1)
    {
        return $query->whereRaw('total_hours - allocated_hours >= ?', [$minHours]);
    }

    /**
     * Recalculate allocated hours from assigned tasks.
     */
    public function recalculateFromTasks(): void
    {
        $weekEnd = $this->week_start->copy()->endOfWeek();

        $allocatedHours = Task::where('assigned_to', $this->user_id)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->where(function ($query) use ($weekEnd) {
                $query->whereBetween('start_date', [$this->week_start, $weekEnd])
                    ->orWhereBetween('due_date', [$this->week_start, $weekEnd])
                    ->orWhere(function ($q) use ($weekEnd) {
                        $q->where('start_date', '<=', $this->week_start)
                            ->where('due_date', '>=', $weekEnd);
                    });
            })
            ->sum('estimated_hours');

        $this->allocated_hours = $allocatedHours;
        $this->save();
    }
}
