<?php

namespace Tests\Browser\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EmailVerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_unverified_user_sees_verification_notice()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/admin/dashboard')
                ->waitForLocation('/email/verify')
                ->assertPathIs('/email/verify')
                ->assertSee('Verify Your Email Address');
        });
    }

    public function test_verified_user_can_access_dashboard()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/admin/dashboard')
                ->assertPathIs('/admin/dashboard')
                ->assertSee('Dashboard');
        });
    }

    public function test_user_can_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $user->roles()->attach($adminRole);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/email/verify')
                ->press('Resend Verification Email')
                ->assertSee('A fresh verification link has been sent');
        });
    }
}
