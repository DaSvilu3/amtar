<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use LogsActivity;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'secondary_phone',
        'address',
        'city',
        'country',
        'tax_number',
        'website',
        'notes',
        'status',
        'relationship_manager_id',
    ];

    /**
     * Get the relationship manager (account manager) for the client.
     */
    public function relationshipManager()
    {
        return $this->belongsTo(User::class, 'relationship_manager_id');
    }

    /**
     * Get the projects for the client.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the contracts for the client.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the files/documents for the client.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'entity_id')
            ->where('entity_type', 'client');
    }

    /**
     * Get a specific document by type slug.
     */
    public function getDocumentByType(string $slug)
    {
        return $this->files()
            ->whereHas('documentType', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->latest()
            ->first();
    }
}
