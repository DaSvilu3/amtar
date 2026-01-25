<?php

namespace Tests\Browser\ProjectManager;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamWorkloadTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $projectManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectManager = User::factory()->create();
        $pmRole = Role::factory()->create(['slug' => 'project-manager', 'name' => 'Project Manager']);
        $this->projectManager->roles()->attach($pmRole);
    }

    public function test_pm_views_team_workload_overview()
    {
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);

        $engineer1 = User::factory()->create();
        $engineer1->roles()->attach($engineerRole);
        Task::factory()->count(5)->create(['assigned_to' => $engineer1->id, 'status' => 'in_progress']);

        $engineer2 = User::factory()->create();
        $engineer2->roles()->attach($engineerRole);
        Task::factory()->count(2)->create(['assigned_to' => $engineer2->id, 'status' => 'in_progress']);

        $this->browse(function (Browser $browser) use ($engineer1, $engineer2) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/team/workload')
                ->assertSee('Team Workload')
                ->assertSee($engineer1->name)
                ->assertSee($engineer2->name)
                ->assertSee('5 tasks')
                ->assertSee('2 tasks');
        });
    }

    public function test_pm_identifies_overloaded_engineers()
    {
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);

        $overloadedEngineer = User::factory()->create();
        $overloadedEngineer->roles()->attach($engineerRole);
        Task::factory()->count(15)->create([
            'assigned_to' => $overloadedEngineer->id,
            'status' => 'in_progress',
        ]);

        $this->browse(function (Browser $browser) use ($overloadedEngineer) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/team/workload')
                ->assertSee('Overloaded')
                ->assertSee($overloadedEngineer->name);
        });
    }

    public function test_pm_views_individual_engineer_capacity()
    {
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer = User::factory()->create(['capacity' => 10]);
        $engineer->roles()->attach($engineerRole);

        Task::factory()->count(7)->create([
            'assigned_to' => $engineer->id,
            'status' => 'in_progress',
        ]);

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/users/' . $engineer->id)
                ->assertSee('Capacity: 7/10')
                ->assertSee('70% Utilized');
        });
    }

    public function test_pm_balances_workload_across_team()
    {
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);

        $heavyEngineer = User::factory()->create();
        $heavyEngineer->roles()->attach($engineerRole);
        $tasks = Task::factory()->count(10)->create([
            'assigned_to' => $heavyEngineer->id,
            'status' => 'pending',
        ]);

        $lightEngineer = User::factory()->create();
        $lightEngineer->roles()->attach($engineerRole);

        $this->browse(function (Browser $browser) use ($tasks, $lightEngineer) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/team/workload')
                ->press('Balance Workload')
                ->waitForText('Workload balanced successfully');
        });

        // Verify some tasks were reassigned
        $reassignedCount = Task::where('assigned_to', $lightEngineer->id)->count();
        $this->assertGreaterThan(0, $reassignedCount);
    }

    public function test_pm_filters_workload_by_project()
    {
        $project1 = \App\Models\Project::factory()->create(['name' => 'Project Alpha']);
        $project2 = \App\Models\Project::factory()->create(['name' => 'Project Beta']);

        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer = User::factory()->create();
        $engineer->roles()->attach($engineerRole);

        Task::factory()->count(5)->create([
            'assigned_to' => $engineer->id,
            'project_id' => $project1->id,
        ]);

        Task::factory()->count(3)->create([
            'assigned_to' => $engineer->id,
            'project_id' => $project2->id,
        ]);

        $this->browse(function (Browser $browser) use ($project1) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/team/workload')
                ->select('project_filter', $project1->id)
                ->press('Filter')
                ->waitForText('5 tasks');
        });
    }
}
