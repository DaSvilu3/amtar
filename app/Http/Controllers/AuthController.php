<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        // First, verify credentials without logging in
        $user = User::where('email', $credentials['email'])->first();

        if ($user && \Hash::check($credentials['password'], $user->password)) {
            // Check if user has 2FA enabled
            if ($user->hasTwoFactorEnabled()) {
                // Store user ID in session for 2FA verification
                $request->session()->put('login.id', $user->id);
                $request->session()->put('login.remember', $remember);

                return redirect()->route('two-factor.challenge');
            }

            // No 2FA, proceed with normal login
            Auth::login($user, $remember);
            $request->session()->regenerate();

            // Update last login timestamp
            $user->update(['last_login_at' => now()]);

            // Log the login activity
            ActivityLog::log('logged_in', $user, null, null, 'User logged in successfully');

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Log the logout activity before logging out
        if (Auth::check()) {
            ActivityLog::log('logged_out', Auth::user(), null, null, 'User logged out');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
