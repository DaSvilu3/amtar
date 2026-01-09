<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view projects list
    }

    /**
     * Determine if user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // Admins and PMs can view all projects
        if ($user->hasAnyRole(['administrator', 'project-manager'])) {
            return true;
        }

        // Engineers can view projects they have tasks assigned to
        return $project->tasks()->where('assigned_to', $user->id)->exists();
    }

    /**
     * Determine if user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can manage the project team.
     */
    public function manageTeam(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }
}
