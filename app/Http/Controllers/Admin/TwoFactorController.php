<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        $user = auth()->user();

        return view('admin.two-factor.show', [
            'enabled' => $user->two_factor_confirmed_at !== null,
            'recoveryCodes' => $user->two_factor_recovery_codes
                ? json_decode(decrypt($user->two_factor_recovery_codes), true)
                : [],
        ]);
    }

    public function enable(Request $request)
    {
        $user = auth()->user();

        // Generate a new secret
        $secret = $this->google2fa->generateSecretKey();

        // Store the secret temporarily (unconfirmed)
        $user->two_factor_secret = encrypt($secret);
        $user->save();

        // Generate QR code
        $qrCode = $this->generateQrCode($secret);

        return view('admin.two-factor.enable', [
            'qrCode' => $qrCode,
            'secret' => $secret,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $secret = decrypt($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'The provided code is invalid.']);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->two_factor_confirmed_at = now();
        $user->save();

        return view('admin.two-factor.confirmed', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('admin.two-factor.show')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = auth()->user();
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
        $user->save();

        return view('admin.two-factor.confirmed', [
            'recoveryCodes' => $recoveryCodes,
            'regenerated' => true,
        ]);
    }

    protected function generateQrCode(string $secret): string
    {
        $user = auth()->user();
        $appName = config('app.name', 'Amtar');

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $appName,
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($qrCodeUrl);
    }

    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }
}
