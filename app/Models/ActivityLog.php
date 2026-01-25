<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSubjectAttribute(): ?Model
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'created' => 'bg-success',
            'updated' => 'bg-info',
            'deleted' => 'bg-danger',
            'restored' => 'bg-warning',
            'logged_in' => 'bg-primary',
            'logged_out' => 'bg-secondary',
            'viewed' => 'bg-light text-dark',
            default => 'bg-secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created' => 'fa-plus-circle',
            'updated' => 'fa-edit',
            'deleted' => 'fa-trash',
            'restored' => 'fa-undo',
            'logged_in' => 'fa-sign-in-alt',
            'logged_out' => 'fa-sign-out-alt',
            'viewed' => 'fa-eye',
            default => 'fa-circle',
        };
    }

    public function getModelLabelAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }

        $class = class_basename($this->model_type);
        return preg_replace('/(?<!^)[A-Z]/', ' $0', $class);
    }

    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'model_name' => $model ? self::getModelDisplayName($model) : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    protected static function getModelDisplayName(Model $model): string
    {
        // Try common name fields
        foreach (['name', 'title', 'subject', 'reference_number', 'email'] as $field) {
            if (isset($model->{$field})) {
                return (string) $model->{$field};
            }
        }
        return class_basename($model) . ' #' . $model->id;
    }
}
