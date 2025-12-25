<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the roles assigned to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the projects managed by the user.
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Get the files uploaded by the user.
     */
    public function uploadedFiles()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    /**
     * Get the contracts created by the user.
     */
    public function createdContracts()
    {
        return $this->hasMany(Contract::class, 'created_by');
    }

    /**
     * Get the tasks assigned to the user.
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get the tasks created by the user.
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get the tasks where user is a reviewer.
     */
    public function reviewTasks()
    {
        return $this->hasMany(Task::class, 'reviewed_by');
    }

    /**
     * Get the skills associated with this user.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
            ->withPivot(['proficiency_level', 'years_experience', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the service stages this user specializes in.
     */
    public function serviceStages()
    {
        return $this->belongsToMany(ServiceStage::class, 'user_service_stage')
            ->withPivot(['expertise_level', 'can_review', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get user capacity records.
     */
    public function capacities()
    {
        return $this->hasMany(UserCapacity::class);
    }

    /**
     * Get primary skills.
     */
    public function primarySkills()
    {
        return $this->skills()->wherePivot('is_primary', true);
    }

    /**
     * Get service stages where user can review.
     */
    public function reviewableStages()
    {
        return $this->serviceStages()->wherePivot('can_review', true);
    }

    /**
     * Check if user has a specific skill.
     */
    public function hasSkill(int $skillId): bool
    {
        return $this->skills()->where('skills.id', $skillId)->exists();
    }

    /**
     * Check if user has all required skills.
     */
    public function hasAllSkills(array $skillIds): bool
    {
        if (empty($skillIds)) {
            return true;
        }
        return $this->skills()->whereIn('skills.id', $skillIds)->count() === count($skillIds);
    }

    /**
     * Check if user can work on a service stage.
     */
    public function canWorkOnStage(int $stageId): bool
    {
        return $this->serviceStages()->where('service_stages.id', $stageId)->exists();
    }

    /**
     * Check if user can review a service stage.
     */
    public function canReviewStage(int $stageId): bool
    {
        return $this->reviewableStages()->where('service_stages.id', $stageId)->exists();
    }

    /**
     * Get available hours for a specific week.
     */
    public function getAvailableHoursForWeek(\Carbon\Carbon $date = null): int
    {
        $capacity = UserCapacity::getOrCreateForWeek($this->id, $date);
        return $capacity->available_hours;
    }

    /**
     * Get current workload (sum of estimated hours on active tasks).
     */
    public function getCurrentWorkload(): int
    {
        return $this->assignedTasks()
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->sum('estimated_hours');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if (is_array($role->permissions) && in_array($permission, $role->permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all permissions for the user.
     */
    public function getAllPermissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            if (is_array($role->permissions)) {
                $permissions = array_merge($permissions, $role->permissions);
            }
        }
        return array_unique($permissions);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a project manager.
     */
    public function isProjectManager(): bool
    {
        return $this->hasRole('project-manager');
    }
}
