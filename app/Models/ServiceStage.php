<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStage extends Model
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
        'sort_order',
    ];

    /**
     * Get the services for the service stage.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the project services for the service stage.
     */
    public function projectServices()
    {
        return $this->hasMany(ProjectService::class);
    }
}
