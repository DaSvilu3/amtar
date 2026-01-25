<?php

namespace Tests\Browser\Shared;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_receives_task_assignment_notification()
    {
        Notification::fake();

        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        $pm = User::factory()->create();
        $pmRole = Role::factory()->create(['slug' => 'project-manager']);
        $pm->roles()->attach($pmRole);

        $task = Task::factory()->create(['assigned_to' => null]);

        $this->browse(function (Browser $browser) use ($task, $engineer, $pm) {
            $browser->loginAs($pm)
                ->visit('/admin/tasks/' . $task->id . '/edit')
                ->select('assigned_to', $engineer->id)
                ->press('Update Task')
                ->waitForText('Task updated successfully');

            $browser->loginAs($engineer)
                ->visit('/admin/notifications')
                ->assertSee('You have been assigned a new task')
                ->assertSee($task->title);
        });
    }

    public function test_user_marks_notification_as_read()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/admin/notifications')
                ->press('Mark as Read')
                ->waitForText('Notification marked as read')
                ->assertDontSee('Unread Notifications');
        });
    }

    public function test_user_views_notification_bell_count()
    {
        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        Task::factory()->count(3)->create(['assigned_to' => $engineer->id]);

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($engineer)
                ->visit('/admin/dashboard')
                ->assertSee('3')
                ->assertVisible('.notification-bell .badge');
        });
    }

    public function test_user_receives_task_due_reminder()
    {
        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        Task::factory()->create([
            'assigned_to' => $engineer->id,
            'due_date' => now()->addDay(),
            'status' => 'in_progress',
        ]);

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($engineer)
                ->visit('/admin/notifications')
                ->assertSee('Task due tomorrow');
        });
    }

    public function test_pm_receives_task_submission_notification()
    {
        $pm = User::factory()->create();
        $pmRole = Role::factory()->create(['slug' => 'project-manager']);
        $pm->roles()->attach($pmRole);

        $engineer = User::factory()->create();
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        $task = Task::factory()->create([
            'assigned_to' => $engineer->id,
            'status' => 'in_progress',
            'progress' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($task, $engineer, $pm) {
            $browser->loginAs($engineer)
                ->visit('/admin/tasks/' . $task->id)
                ->press('Submit for Review')
                ->waitForText('Task submitted for review');

            $browser->loginAs($pm)
                ->visit('/admin/notifications')
                ->assertSee('Task submitted for review')
                ->assertSee($task->title);
        });
    }
}
