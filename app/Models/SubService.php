<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'main_service_id',
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
     * Get the main service that owns the sub-service.
     */
    public function mainService()
    {
        return $this->belongsTo(MainService::class);
    }

    /**
     * Get the service packages for the sub-service.
     */
    public function servicePackages()
    {
        return $this->hasMany(ServicePackage::class);
    }

    /**
     * Get the projects for the sub-service.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
