<?php

namespace Tests\Browser\ProjectManager;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskApprovalTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $projectManager;
    private User $engineer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectManager = User::factory()->create();
        $pmRole = Role::factory()->create(['slug' => 'project-manager', 'name' => 'Project Manager']);
        $this->projectManager->roles()->attach($pmRole);

        $this->engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $this->engineer->roles()->attach($engineerRole);
    }

    public function test_pm_approves_submitted_task()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'review',
            'progress' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Task Details')
                ->assertSee('Status: Review')
                ->press('Approve Task')
                ->waitForText('Task approved successfully');
        });

        $task->refresh();
        $this->assertEquals('completed', $task->status);
    }

    public function test_pm_requests_changes_on_task()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'review',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Request Changes')
                ->waitFor('#review-comments-modal')
                ->type('review_comments', 'Please update the documentation section.')
                ->press('Submit Feedback')
                ->waitForText('Feedback submitted successfully');
        });

        $task->refresh();
        $this->assertEquals('in_progress', $task->status);
        $this->assertNotNull($task->review_comments);
    }

    public function test_pm_views_tasks_pending_review()
    {
        Task::factory()->count(3)->create([
            'status' => 'review',
            'assigned_to' => $this->engineer->id,
        ]);

        Task::factory()->count(2)->create([
            'status' => 'in_progress',
            'assigned_to' => $this->engineer->id,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks?status=review')
                ->assertSee('Tasks Pending Review')
                ->assertSeeIn('.task-count', '3');
        });
    }

    public function test_pm_bulk_approves_multiple_tasks()
    {
        $tasks = Task::factory()->count(5)->create([
            'status' => 'review',
            'assigned_to' => $this->engineer->id,
            'progress' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($tasks) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks?status=review');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->select('bulk_action', 'approve')
                ->press('Apply')
                ->waitForText('Tasks approved successfully');
        });

        foreach ($tasks as $task) {
            $task->refresh();
            $this->assertEquals('completed', $task->status);
        }
    }

    public function test_pm_adds_review_notes_to_task()
    {
        $task = Task::factory()->create([
            'status' => 'review',
            'assigned_to' => $this->engineer->id,
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->projectManager)
                ->visit('/admin/tasks/' . $task->id)
                ->type('review_notes', 'Excellent work! Code quality is high.')
                ->press('Add Review Notes')
                ->waitForText('Notes added successfully');
        });

        $task->refresh();
        $this->assertStringContainsString('Excellent work', $task->review_notes);
    }
}
