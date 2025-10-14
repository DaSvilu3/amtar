<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'entity_type',
        'is_required',
        'description',
        'file_types',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'file_types' => 'array',
        ];
    }

    /**
     * Get all files of this document type.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Scope a query to only include active document types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by entity type.
     */
    public function scopeForEntity($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include required document types.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
