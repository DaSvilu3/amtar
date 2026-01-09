<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNote extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'content',
        'type',
        'is_pinned',
        'reminder_date',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'reminder_date' => 'date',
        ];
    }

    /**
     * Get the project that owns the note.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for notes only.
     */
    public function scopeNotes($query)
    {
        return $query->where('type', 'note');
    }

    /**
     * Scope for comments only.
     */
    public function scopeComments($query)
    {
        return $query->where('type', 'comment');
    }

    /**
     * Scope for reminders only.
     */
    public function scopeReminders($query)
    {
        return $query->where('type', 'reminder');
    }

    /**
     * Scope for pinned notes.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for calendar events (notes with reminder dates).
     */
    public function scopeCalendarEvents($query)
    {
        return $query->whereNotNull('reminder_date');
    }

    /**
     * Scope for upcoming reminders.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('reminder_date')
            ->where('reminder_date', '>=', now()->toDateString());
    }

    /**
     * Get color classes for calendar.
     */
    public function getColorClassAttribute(): string
    {
        return match($this->color) {
            'red' => 'bg-danger',
            'yellow' => 'bg-warning',
            'green' => 'bg-success',
            'blue' => 'bg-primary',
            'purple' => 'bg-purple',
            default => 'bg-info',
        };
    }
}
