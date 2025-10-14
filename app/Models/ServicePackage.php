<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'main_service_id',
        'sub_service_id',
        'slug',
        'name',
        'description',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the main service that owns the service package.
     */
    public function mainService()
    {
        return $this->belongsTo(MainService::class);
    }

    /**
     * Get the sub-service that owns the service package.
     */
    public function subService()
    {
        return $this->belongsTo(SubService::class);
    }

    /**
     * Get the services that belong to the service package.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service')
            ->withPivot('service_stage_id', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get the projects for the service package.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
