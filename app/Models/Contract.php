<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'contract_number',
        'title',
        'client_id',
        'project_id',
        'description',
        'value',
        'currency',
        'start_date',
        'end_date',
        'status',
        'file_path',
        'terms',
        'services',
        'auto_generated',
        'signed_date',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'signed_date' => 'date',
            'services' => 'array',
            'auto_generated' => 'boolean',
        ];
    }

    /**
     * Get the client that owns the contract.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project associated with the contract.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the contract.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the project services through the project relationship.
     */
    public function projectServices()
    {
        return $this->hasManyThrough(
            ProjectService::class,
            Project::class,
            'id',           // Foreign key on projects table
            'project_id',   // Foreign key on project_services table
            'project_id',   // Local key on contracts table
            'id'            // Local key on projects table
        );
    }
}
