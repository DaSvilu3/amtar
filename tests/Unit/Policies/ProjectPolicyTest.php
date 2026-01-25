<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Role;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pm;
    private User $engineer;
    private Role $adminRole;
    private Role $pmRole;
    private Role $engineerRole;
    private ProjectPolicy $policy;

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

        $this->policy = new ProjectPolicy();
    }

    public function test_admin_can_view_any_project(): void
    {
        $this->assertTrue($this->policy->viewAny($this->admin));
    }

    public function test_pm_can_view_any_project(): void
    {
        $this->assertTrue($this->policy->viewAny($this->pm));
    }

    public function test_engineer_can_view_any_project(): void
    {
        $this->assertTrue($this->policy->viewAny($this->engineer));
    }

    public function test_admin_can_view_all_projects(): void
    {
        $project = Project::factory()->create(['project_manager_id' => $this->pm->id]);

        $this->assertTrue($this->policy->view($this->admin, $project));
    }

    public function test_pm_can_view_own_projects(): void
    {
        $project = Project::factory()->create(['project_manager_id' => $this->pm->id]);

        $this->assertTrue($this->policy->view($this->pm, $project));
    }

    public function test_pm_can_view_others_projects(): void
    {
        $otherPm = User::factory()->create();
        $otherPm->roles()->attach($this->pmRole);

        $project = Project::factory()->create(['project_manager_id' => $otherPm->id]);

        $this->assertTrue($this->policy->view($this->pm, $project));
    }

    public function test_engineer_can_view_any_project(): void
    {
        $project = Project::factory()->create(['project_manager_id' => $this->pm->id]);

        $this->assertTrue($this->policy->view($this->engineer, $project));
    }

    public function test_admin_can_create_projects(): void
    {
        $this->assertTrue($this->policy->create($this->admin));
    }

    public function test_pm_can_create_projects(): void
    {
        $this->assertTrue($this->policy->create($this->pm));
    }

    public function test_engineer_cannot_create_projects(): void
    {
        $this->assertFalse($this->policy->create($this->engineer));
    }

    public function test_admin_can_update_any_project(): void
    {
        $project = Project::factory()->create();

        $this->assertTrue($this->policy->update($this->admin, $project));
    }

    public function test_pm_can_update_own_projects(): void
    {
        $project = Project::factory()->create(['project_manager_id' => $this->pm->id]);

        $this->assertTrue($this->policy->update($this->pm, $project));
    }

    public function test_pm_can_update_others_projects(): void
    {
        $otherPm = User::factory()->create();
        $otherPm->roles()->attach($this->pmRole);

        $project = Project::factory()->create(['project_manager_id' => $otherPm->id]);

        $this->assertTrue($this->policy->update($this->pm, $project));
    }

    public function test_engineer_cannot_update_projects(): void
    {
        $project = Project::factory()->create();

        $this->assertFalse($this->policy->update($this->engineer, $project));
    }

    public function test_admin_can_delete_projects(): void
    {
        $project = Project::factory()->create();

        $this->assertTrue($this->policy->delete($this->admin, $project));
    }

    public function test_pm_can_delete_own_projects(): void
    {
        $project = Project::factory()->create(['project_manager_id' => $this->pm->id]);

        $this->assertTrue($this->policy->delete($this->pm, $project));
    }

    public function test_pm_can_delete_others_projects(): void
    {
        $otherPm = User::factory()->create();
        $otherPm->roles()->attach($this->pmRole);

        $project = Project::factory()->create(['project_manager_id' => $otherPm->id]);

        $this->assertTrue($this->policy->delete($this->pm, $project));
    }

    public function test_engineer_cannot_delete_projects(): void
    {
        $project = Project::factory()->create();

        $this->assertFalse($this->policy->delete($this->engineer, $project));
    }
}
