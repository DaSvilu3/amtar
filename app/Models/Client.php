<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
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
    ];

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
}
