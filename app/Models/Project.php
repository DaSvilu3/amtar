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
}
