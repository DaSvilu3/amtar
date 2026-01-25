<?php

namespace Tests\Browser\Administrator;

use App\Models\Role;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserManagementTest extends DuskTestCase
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

    public function test_admin_can_create_new_user()
    {
        $engineerRole = Role::factory()->create(['slug' => 'engineer', 'name' => 'Engineer']);
        $skill = Skill::factory()->create(['name' => 'PHP Development']);

        $this->browse(function (Browser $browser) use ($engineerRole, $skill) {
            $browser->loginAs($this->admin)
                ->visit('/admin/users/create')
                ->assertSee('Create New User')
                ->type('name', 'Ahmed AlMaamari')
                ->type('email', 'ahmed@amtar.om')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->select('role_id', $engineerRole->id)
                ->check('skills[]', $skill->id)
                ->press('Create User')
                ->waitForText('User created successfully')
                ->assertPathIs('/admin/users');
        });

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmed AlMaamari',
            'email' => 'ahmed@amtar.om',
        ]);

        $user = User::where('email', 'ahmed@amtar.om')->first();
        $this->assertTrue($user->roles->contains($engineerRole));
        $this->assertTrue($user->skills->contains($skill));
    }

    public function test_admin_can_edit_existing_user()
    {
        $engineer = User::factory()->create(['name' => 'Original Name']);
        $engineerRole = Role::factory()->create(['slug' => 'engineer']);
        $engineer->roles()->attach($engineerRole);

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($this->admin)
                ->visit('/admin/users/' . $engineer->id . '/edit')
                ->assertSee('Edit User')
                ->clear('name')
                ->type('name', 'Updated Name')
                ->press('Update User')
                ->waitForText('User updated successfully')
                ->assertPathIs('/admin/users');
        });

        $engineer->refresh();
        $this->assertEquals('Updated Name', $engineer->name);
    }

    public function test_admin_can_deactivate_user()
    {
        $engineer = User::factory()->create(['is_active' => true]);

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($this->admin)
                ->visit('/admin/users')
                ->press('Deactivate')
                ->waitForText('User deactivated successfully');
        });

        $engineer->refresh();
        $this->assertFalse($engineer->is_active);
    }

    public function test_admin_can_view_user_workload()
    {
        $engineer = User::factory()->create();

        $this->browse(function (Browser $browser) use ($engineer) {
            $browser->loginAs($this->admin)
                ->visit('/admin/users/' . $engineer->id)
                ->assertSee('User Details')
                ->assertSee('Current Workload')
                ->assertSee('Assigned Tasks');
        });
    }

    public function test_admin_cannot_delete_own_account()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit('/admin/users/' . $this->admin->id)
                ->assertDontSee('Delete User');
        });
    }
}
