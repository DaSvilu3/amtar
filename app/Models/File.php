<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'category',
        'description',
        'uploaded_by',
        'is_public',
        'document_type_id',
        'entity_type',
        'entity_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the document type of the file.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the parent entity (polymorphic relationship).
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Get the entity for this file (manual polymorphic).
     */
    public function getEntityAttribute()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . ucfirst($this->entity_type);

        if (class_exists($modelClass)) {
            return $modelClass::find($this->entity_id);
        }

        return null;
    }
}
