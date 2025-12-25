<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    protected $fillable = [
        'service_id',
        'service_stage_id',
        'title',
        'description',
        'priority',
        'estimated_hours',
        'default_duration_days',
        'required_skills',
        'required_expertise_level',
        'requires_review',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'requires_review' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the service this template belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the service stage this template belongs to.
     */
    public function serviceStage()
    {
        return $this->belongsTo(ServiceStage::class);
    }

    /**
     * Get the tasks created from this template.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get templates this template depends on.
     */
    public function dependencies()
    {
        return $this->belongsToMany(TaskTemplate::class, 'task_template_dependencies', 'task_template_id', 'depends_on_template_id')
            ->withTimestamps();
    }

    /**
     * Get templates that depend on this template.
     */
    public function dependents()
    {
        return $this->belongsToMany(TaskTemplate::class, 'task_template_dependencies', 'depends_on_template_id', 'task_template_id')
            ->withTimestamps();
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for templates by service.
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Create a task from this template.
     */
    public function createTask(Project $project, ?ProjectService $projectService = null, ?Milestone $milestone = null, ?int $assignedTo = null): Task
    {
        $startDate = now();
        $dueDate = $this->default_duration_days ? now()->addDays($this->default_duration_days) : null;

        return Task::create([
            'project_id' => $project->id,
            'project_service_id' => $projectService?->id,
            'milestone_id' => $milestone?->id,
            'task_template_id' => $this->id,
            'assigned_to' => $assignedTo,
            'created_by' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'estimated_hours' => $this->estimated_hours,
            'requires_review' => $this->requires_review,
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'sort_order' => $this->sort_order,
        ]);
    }

    /**
     * Get required skill models.
     */
    public function getRequiredSkillModels()
    {
        if (empty($this->required_skills)) {
            return collect();
        }

        return Skill::whereIn('id', $this->required_skills)->get();
    }
}
