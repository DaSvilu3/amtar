<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view tasks list
    }

    /**
     * Determine if user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins and PMs can view all tasks
        if ($user->hasAnyRole(['administrator', 'project-manager'])) {
            return true;
        }

        // Engineers can only view tasks assigned to them or they're reviewing
        return $task->assigned_to === $user->id || $task->reviewed_by === $user->id;
    }

    /**
     * Determine if user can create tasks.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins and PMs can edit all tasks
        if ($user->hasAnyRole(['administrator', 'project-manager'])) {
            return true;
        }

        // Engineers can only update status/progress on their assigned tasks
        return false;
    }

    /**
     * Determine if user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can assign the task.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->hasAnyRole(['administrator', 'project-manager']);
    }

    /**
     * Determine if user can approve/reject the task.
     */
    public function review(User $user, Task $task): bool
    {
        // Admins and PMs can review any task
        if ($user->hasAnyRole(['administrator', 'project-manager'])) {
            return true;
        }

        // The assigned reviewer can review
        return $task->reviewed_by === $user->id;
    }

    /**
     * Determine if user can update progress on the task.
     */
    public function updateProgress(User $user, Task $task): bool
    {
        // Admins and PMs can update progress on any task
        if ($user->hasAnyRole(['administrator', 'project-manager'])) {
            return true;
        }

        // Engineers can update progress on their assigned tasks
        return $task->assigned_to === $user->id;
    }
}
