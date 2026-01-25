<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected static array $logAttributes = ['*'];
    protected static array $ignoreAttributes = ['updated_at', 'remember_token'];

    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    protected function logActivity(string $action): void
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $changes = $this->getActivityChanges();
            if (empty($changes['old']) && empty($changes['new'])) {
                return; // No meaningful changes
            }
            $oldValues = $changes['old'];
            $newValues = $changes['new'];
        } elseif ($action === 'created') {
            $newValues = $this->getLoggableAttributes($this->getAttributes());
        } elseif ($action === 'deleted') {
            $oldValues = $this->getLoggableAttributes($this->getAttributes());
        }

        ActivityLog::log($action, $this, $oldValues, $newValues);
    }

    protected function getActivityChanges(): array
    {
        $dirty = $this->getDirty();
        $original = $this->getOriginal();

        $old = [];
        $new = [];

        foreach ($dirty as $key => $value) {
            if ($this->shouldLogAttribute($key)) {
                $old[$key] = $original[$key] ?? null;
                $new[$key] = $value;
            }
        }

        return ['old' => $old, 'new' => $new];
    }

    protected function getLoggableAttributes(array $attributes): array
    {
        return collect($attributes)
            ->filter(fn ($value, $key) => $this->shouldLogAttribute($key))
            ->toArray();
    }

    protected function shouldLogAttribute(string $key): bool
    {
        $logAttributes = static::$logAttributes ?? ['*'];
        $ignoreAttributes = static::$ignoreAttributes ?? ['updated_at', 'remember_token'];

        if (in_array($key, $ignoreAttributes)) {
            return false;
        }

        if ($logAttributes === ['*'] || in_array('*', $logAttributes)) {
            return true;
        }

        return in_array($key, $logAttributes);
    }
}
