<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show(Request $request)
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('login.id'));

        if (!$user) {
            return redirect()->route('login');
        }

        // Try OTP code first
        if ($request->filled('code')) {
            $secret = decrypt($user->two_factor_secret);

            if ($this->google2fa->verifyKey($secret, $request->code)) {
                return $this->loginUser($request, $user);
            }

            return back()->withErrors(['code' => 'The provided two-factor authentication code is invalid.']);
        }

        // Try recovery code
        if ($request->filled('recovery_code')) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

            if (in_array($request->recovery_code, $recoveryCodes)) {
                // Remove used recovery code
                $recoveryCodes = array_diff($recoveryCodes, [$request->recovery_code]);
                $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
                $user->save();

                return $this->loginUser($request, $user);
            }

            return back()->withErrors(['recovery_code' => 'The provided recovery code is invalid.']);
        }

        return back()->withErrors(['code' => 'Please provide a code or recovery code.']);
    }

    protected function loginUser(Request $request, User $user)
    {
        Auth::login($user, $request->session()->get('login.remember', false));

        $request->session()->forget(['login.id', 'login.remember']);
        $request->session()->regenerate();

        // Log the login activity
        \App\Models\ActivityLog::log('logged_in', $user, null, null, 'User logged in with 2FA');

        return redirect()->intended(route('admin.dashboard'));
    }
}
