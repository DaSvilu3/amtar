<?php

namespace Tests\Browser\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@amtar.om',
            'password' => bcrypt('password123'),
        ]);

        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'test@amtar.om')
                ->type('password', 'password123')
                ->press('Login')
                ->waitForLocation('/admin/dashboard')
                ->assertPathIs('/admin/dashboard')
                ->assertSee('Dashboard');
        });
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'invalid@amtar.om')
                ->type('password', 'wrongpassword')
                ->press('Login')
                ->assertPathIs('/login')
                ->assertSee('These credentials do not match our records');
        });
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/admin/dashboard')
                ->click('a[href="' . route('logout') . '"]')
                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }
}
