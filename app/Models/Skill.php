<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users that have this skill.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')
            ->withPivot(['proficiency_level', 'years_experience', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Scope for active skills.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for skills by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get users with this skill at a minimum proficiency level.
     */
    public function getUsersWithMinProficiency(string $level)
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
        $minLevel = $levels[$level] ?? 1;

        return $this->users()
            ->wherePivotIn('proficiency_level', array_keys(array_filter($levels, fn($l) => $l >= $minLevel)));
    }
}
