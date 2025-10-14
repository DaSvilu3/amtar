<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
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
     * Get the sub-services for the main service.
     */
    public function subServices()
    {
        return $this->hasMany(SubService::class);
    }

    /**
     * Get the service packages for the main service.
     */
    public function servicePackages()
    {
        return $this->hasMany(ServicePackage::class);
    }

    /**
     * Get the projects for the main service.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
