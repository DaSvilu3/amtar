<?php

namespace Tests\Browser\ProjectManager;

use App\Models\Project;
use App\Models\Role;
use App\Models\Skill;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskAssignmentTest extends DuskTestCase
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

    public function test_pm_manually_assigns_task()
    {
        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        $task = Task::factory()->create(['assigned_to' => null]);

        $this->browse(function (Browser $browser) use ($task, $engineer) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id . '/edit')
                ->assertSee('Edit Task')
                ->select('assigned_to', $engineer->id)
                ->press('Update Task')
                ->waitForText('Task updated successfully');
        });

        $task->refresh();
        $this->assertEquals($engineer->id, $task->assigned_to);
    }

    public function test_pm_uses_ai_assignment_suggestions()
    {
        $skill = Skill::factory()->create(['name' => 'Laravel Development']);

        $engineer1 = User::factory()->create();
        $engineer1->skills()->attach($skill->id, [
            'proficiency_level' => 'expert',
            'years_experience' => 5,
        ]);

        $engineer2 = User::factory()->create();
        $engineer2->skills()->attach($skill->id, [
            'proficiency_level' => 'intermediate',
            'years_experience' => 2,
        ]);

        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer1->roles()->attach($engineerRole);
        $engineer2->roles()->attach($engineerRole);

        $task = Task::factory()->create(['assigned_to' => null]);

        $this->browse(function (Browser $browser) use ($task, $engineer1) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id . '/edit')
                ->press('Get AI Suggestion')
                ->waitForText('Recommended Assignee')
                ->assertSee($engineer1->name)
                ->press('Use Suggestion')
                ->waitForText('Task assigned successfully');
        });

        $task->refresh();
        $this->assertEquals($engineer1->id, $task->assigned_to);
    }

    public function test_pm_reassigns_task_to_different_engineer()
    {
        $engineer1 = User::factory()->create();
        $engineer2 = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer1->roles()->attach($engineerRole);
        $engineer2->roles()->attach($engineerRole);

        $task = Task::factory()->create(['assigned_to' => $engineer1->id]);

        $this->browse(function (Browser $browser) use ($task, $engineer2) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id . '/edit')
                ->select('assigned_to', $engineer2->id)
                ->press('Update Task')
                ->waitForText('Task updated successfully');
        });

        $task->refresh();
        $this->assertEquals($engineer2->id, $task->assigned_to);
    }

    public function test_pm_views_task_assignment_history()
    {
        $task = Task::factory()->create();

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Task Details')
                ->assertSee('Assignment History');
        });
    }

    public function test_pm_auto_assigns_multiple_tasks()
    {
        $tasks = Task::factory()->count(5)->create(['assigned_to' => null]);

        $this->browse(function (Browser $browser) use ($tasks) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->press('Auto-Assign Selected')
                ->waitForText('Tasks auto-assigned successfully');
        });

        foreach ($tasks as $task) {
            $task->refresh();
            $this->assertNotNull($task->assigned_to);
        }
    }
}
