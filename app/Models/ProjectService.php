<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'service_id',
        'service_stage_id',
        'is_from_package',
        'is_completed',
        'completed_at',
        'notes',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_from_package' => 'boolean',
            'is_completed' => 'boolean',
            'completed_at' => 'date',
        ];
    }

    /**
     * Get the project that owns the service.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the service that owns the project service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the service stage that owns the project service.
     */
    public function serviceStage()
    {
        return $this->belongsTo(ServiceStage::class);
    }

    /**
     * Get the tasks associated with this project service.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
