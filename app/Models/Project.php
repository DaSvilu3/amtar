<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'project_number',
        'client_id',
        'description',
        'status',
        'budget',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'project_manager_id',
        'main_service_id',
        'sub_service_id',
        'service_package_id',
        'location',
        'progress',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
        ];
    }

    /**
     * Get the client that owns the project.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project manager for the project.
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Get the services for the project.
     */
    public function services()
    {
        return $this->hasMany(ProjectService::class);
    }

    /**
     * Get the contracts for the project.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the main service for the project.
     */
    public function mainService()
    {
        return $this->belongsTo(MainService::class);
    }

    /**
     * Get the sub-service for the project.
     */
    public function subService()
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * Get the service package for the project.
     */
    public function servicePackage()
    {
        return $this->belongsTo(ServicePackage::class);
    }

    /**
     * Get the files/documents for the project.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'entity_id')
            ->where('entity_type', 'project');
    }

    /**
     * Get the documents for the project (alias for files).
     */
    public function documents()
    {
        return $this->files();
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the milestones for the project.
     */
    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Calculate overall project progress based on tasks.
     */
    public function calculateTaskProgress(): int
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return $this->progress ?? 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return (int) round(($completedTasks / $totalTasks) * 100);
    }
}
