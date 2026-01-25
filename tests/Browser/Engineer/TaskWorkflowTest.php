<?php

namespace Tests\Browser\Engineer;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TaskWorkflowTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $engineer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer', 'name' => 'Engineer']);
        $this->engineer->roles()->attach($engineerRole);
    }

    public function test_engineer_views_assigned_tasks()
    {
        Task::factory()->count(3)->create(['assigned_to' => $this->engineer->id]);
        Task::factory()->count(2)->create(['assigned_to' => null]); // Unassigned

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks')
                ->assertSee('My Tasks')
                ->assertSeeIn('.task-count', '3');
        });
    }

    public function test_engineer_starts_task()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Task Details')
                ->press('Start Task')
                ->waitForText('Task started successfully');
        });

        $task->refresh();
        $this->assertEquals('in_progress', $task->status);
    }

    public function test_engineer_updates_task_progress()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'in_progress',
            'progress' => 25,
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->type('progress', '75')
                ->press('Update Progress')
                ->waitForText('Progress updated successfully');
        });

        $task->refresh();
        $this->assertEquals(75, $task->progress);
    }

    public function test_engineer_submits_task_for_review()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'in_progress',
            'progress' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Submit for Review')
                ->waitForText('Task submitted for review');
        });

        $task->refresh();
        $this->assertEquals('review', $task->status);
    }

    public function test_engineer_cannot_submit_incomplete_task()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'in_progress',
            'progress' => 50,
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Submit for Review')
                ->waitForText('Task must be 100% complete before submission');
        });

        $task->refresh();
        $this->assertEquals('in_progress', $task->status);
    }

    public function test_engineer_handles_review_feedback()
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'in_progress',
            'review_comments' => 'Please update the documentation.',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Review Feedback')
                ->assertSee('Please update the documentation')
                ->press('Mark Feedback as Addressed')
                ->waitForText('Feedback addressed');
        });
    }

    public function test_engineer_views_task_dependencies()
    {
        $blockingTask = Task::factory()->create(['status' => 'pending']);
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'pending',
        ]);

        $task->dependencies()->attach($blockingTask->id);

        $this->browse(function (Browser $browser) use ($task, $blockingTask) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->assertSee('Dependencies')
                ->assertSee($blockingTask->title)
                ->assertSee('This task is blocked by');
        });
    }

    public function test_engineer_cannot_start_blocked_task()
    {
        $blockingTask = Task::factory()->create(['status' => 'in_progress']);
        $task = Task::factory()->create([
            'assigned_to' => $this->engineer->id,
            'status' => 'pending',
        ]);

        $task->dependencies()->attach($blockingTask->id);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->loginAs($this->engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Start Task')
                ->waitForText('Cannot start task with incomplete dependencies');
        });

        $task->refresh();
        $this->assertEquals('pending', $task->status);
    }
}
