<?php

namespace Tests\Browser\Administrator;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BulkOperationsTest extends DuskTestCase
{
    use DatabaseMigrations;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator', 'name' => 'Administrator']);
        $this->admin->roles()->attach($adminRole);
    }

    public function test_admin_bulk_assigns_tasks()
    {
        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        $tasks = Task::factory()->count(5)->create(['assigned_to' => null]);

        $this->browse(function (Browser $browser) use ($engineer, $tasks) {
            $browser->loginAs($this->admin)
                ->visit('/admin/tasks')
                ->assertSee('Tasks');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->select('bulk_action', 'assign')
                ->select('assign_to', $engineer->id)
                ->press('Apply')
                ->waitForText('Tasks updated successfully');
        });

        foreach ($tasks as $task) {
            $task->refresh();
            $this->assertEquals($engineer->id, $task->assigned_to);
        }
    }

    public function test_admin_bulk_updates_task_status()
    {
        $tasks = Task::factory()->count(5)->create(['status' => 'pending']);

        $this->browse(function (Browser $browser) use ($tasks) {
            $browser->loginAs($this->admin)
                ->visit('/admin/tasks');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->select('bulk_action', 'update_status')
                ->select('status', 'in_progress')
                ->press('Apply')
                ->waitForText('Tasks updated successfully');
        });

        foreach ($tasks as $task) {
            $task->refresh();
            $this->assertEquals('in_progress', $task->status);
        }
    }

    public function test_admin_bulk_updates_task_priority()
    {
        $tasks = Task::factory()->count(5)->create(['priority' => 'medium']);

        $this->browse(function (Browser $browser) use ($tasks) {
            $browser->loginAs($this->admin)
                ->visit('/admin/tasks');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->select('bulk_action', 'update_priority')
                ->select('priority', 'high')
                ->press('Apply')
                ->waitForText('Tasks updated successfully');
        });

        foreach ($tasks as $task) {
            $task->refresh();
            $this->assertEquals('high', $task->priority);
        }
    }

    public function test_admin_bulk_deletes_tasks()
    {
        $tasks = Task::factory()->count(3)->create();
        $taskIds = $tasks->pluck('id')->toArray();

        $this->browse(function (Browser $browser) use ($tasks) {
            $browser->loginAs($this->admin)
                ->visit('/admin/tasks');

            foreach ($tasks as $task) {
                $browser->check('task_ids[]', $task->id);
            }

            $browser->select('bulk_action', 'delete')
                ->press('Apply')
                ->waitForDialog()
                ->acceptDialog()
                ->waitForText('Tasks deleted successfully');
        });

        foreach ($taskIds as $id) {
            $this->assertDatabaseMissing('tasks', ['id' => $id]);
        }
    }
}
