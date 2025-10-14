<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'service_stage_id',
        'slug',
        'name',
        'description',
        'is_optional',
        'required_documents',
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
            'is_optional' => 'boolean',
            'required_documents' => 'array',
        ];
    }

    /**
     * Get the service stage that owns the service.
     */
    public function serviceStage()
    {
        return $this->belongsTo(ServiceStage::class);
    }

    /**
     * Get the service packages that belong to the service.
     */
    public function servicePackages()
    {
        return $this->belongsToMany(ServicePackage::class, 'package_service')
            ->withPivot('service_stage_id', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Get the project services for the service.
     */
    public function projectServices()
    {
        return $this->hasMany(ProjectService::class);
    }
}
