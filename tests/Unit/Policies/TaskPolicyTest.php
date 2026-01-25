<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Role;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pm;
    private User $engineer;
    private Role $adminRole;
    private Role $pmRole;
    private Role $engineerRole;
    private TaskPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::factory()->administrator()->create();
        $this->pmRole = Role::factory()->projectManager()->create();
        $this->engineerRole = Role::factory()->engineer()->create();

        // Create users with roles
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($this->adminRole);

        $this->pm = User::factory()->create();
        $this->pm->roles()->attach($this->pmRole);

        $this->engineer = User::factory()->create();
        $this->engineer->roles()->attach($this->engineerRole);

        $this->policy = new TaskPolicy();
    }

    public function test_admin_can_view_any_task(): void
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    public function test_pm_can_view_any_task(): void
    {
        $this->assertTrue($this->policy->viewAny($this->pm));
    }

    public function test_engineer_can_view_any_task(): void
    {
        $this->assertTrue($this->policy->viewAny($this->engineer));
    }

    public function test_admin_can_view_all_tasks(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->view($this->admin, $task));
    }

    public function test_pm_can_view_all_tasks(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->view($this->pm, $task));
    }

    public function test_engineer_cannot_view_unassigned_tasks(): void
    {
        $task = Task::factory()->create(['assigned_to' => null]);

        $this->assertFalse($this->policy->view($this->engineer, $task));
    }

    public function test_engineer_can_view_assigned_tasks(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->view($this->engineer, $task));
    }

    public function test_engineer_can_view_tasks_they_review(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->pm->id,
            'reviewed_by' => $this->engineer->id
        ]);

        $this->assertTrue($this->policy->view($this->engineer, $task));
    }

    public function test_only_admin_can_create_tasks(): void
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    public function test_pm_can_create_tasks(): void
    {
        $this->assertTrue($this->policy->create($this->pm));
    }

    public function test_engineer_cannot_create_tasks(): void
    {
        $this->assertFalse($this->policy->create($this->engineer));
    }

    public function test_admin_can_update_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->update($this->admin, $task));
    }

    public function test_pm_can_update_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->update($this->pm, $task));
    }

    public function test_engineer_cannot_update_task_details(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertFalse($this->policy->update($this->engineer, $task));
    }

    public function test_admin_can_delete_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->delete($this->admin, $task));
    }

    public function test_pm_can_delete_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->delete($this->pm, $task));
    }

    public function test_engineer_cannot_delete_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertFalse($this->policy->delete($this->engineer, $task));
    }

    public function test_admin_can_assign_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->assign($this->admin, $task));
    }

    public function test_pm_can_assign_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertTrue($this->policy->assign($this->pm, $task));
    }

    public function test_engineer_cannot_assign_tasks(): void
    {
        $task = Task::factory()->create();

        $this->assertFalse($this->policy->assign($this->engineer, $task));
    }

    public function test_admin_can_review_any_task(): void
    {
        $task = Task::factory()->create(['requires_review' => true]);

        $this->assertTrue($this->policy->review($this->admin, $task));
    }

    public function test_pm_can_review_any_task(): void
    {
        $task = Task::factory()->create(['requires_review' => true]);

        $this->assertTrue($this->policy->review($this->pm, $task));
    }

    public function test_reviewer_can_review_assigned_tasks(): void
    {
        $task = Task::factory()->create([
            'requires_review' => true,
            'reviewed_by' => $this->engineer->id
        ]);

        $this->assertTrue($this->policy->review($this->engineer, $task));
    }

    public function test_non_reviewer_cannot_review_tasks(): void
    {
        $otherEngineer = User::factory()->create();
        $otherEngineer->roles()->attach($this->engineerRole);

        $task = Task::factory()->create([
            'requires_review' => true,
            'reviewed_by' => $this->engineer->id
        ]);

        $this->assertFalse($this->policy->review($otherEngineer, $task));
    }

    public function test_engineer_can_update_own_task_progress(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->updateProgress($this->engineer, $task));
    }

    public function test_engineer_cannot_update_others_task_progress(): void
    {
        $otherEngineer = User::factory()->create();
        $otherEngineer->roles()->attach($this->engineerRole);

        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertFalse($this->policy->updateProgress($otherEngineer, $task));
    }

    public function test_admin_can_update_any_task_progress(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->updateProgress($this->admin, $task));
    }

    public function test_pm_can_update_any_task_progress(): void
    {
        $task = Task::factory()->create(['assigned_to' => $this->engineer->id]);

        $this->assertTrue($this->policy->updateProgress($this->pm, $task));
    }
}
