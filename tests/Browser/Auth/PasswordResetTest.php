<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PasswordResetTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_request_password_reset_link()
    {
        $user = User::factory()->create(['email' => 'reset@amtar.om']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->clickLink('Forgot Your Password?')
                ->waitForLocation('/password/reset')
                ->assertPathIs('/password/reset')
                ->type('email', 'reset@amtar.om')
                ->press('Send Password Reset Link')
                ->assertSee('We have emailed your password reset link');
        });
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'reset@amtar.om',
            'password' => bcrypt('oldpassword'),
        ]);

        $token = Password::broker()->createToken($user);

        $this->browse(function (Browser $browser) use ($token) {
            $browser->visit('/password/reset/' . $token)
                ->type('email', 'reset@amtar.om')
                ->type('password', 'newpassword123')
                ->type('password_confirmation', 'newpassword123')
                ->press('Reset Password')
                ->waitForLocation('/admin/dashboard')
                ->assertPathIs('/admin/dashboard');
        });

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_password_reset_fails_with_invalid_token()
    {
        User::factory()->create(['email' => 'reset@amtar.om']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/password/reset/invalid-token')
                ->type('email', 'reset@amtar.om')
                ->type('password', 'newpassword123')
                ->type('password_confirmation', 'newpassword123')
                ->press('Reset Password')
                ->assertSee('This password reset token is invalid');
        });
    }
}
