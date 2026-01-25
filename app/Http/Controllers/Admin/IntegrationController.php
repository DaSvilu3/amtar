<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Integrations\EmailService;
use App\Services\Integrations\WhatsAppService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        $email = app(EmailService::class);
        $whatsapp = app(WhatsAppService::class);

        $integrations = [
            [
                'name' => 'Email',
                'type' => 'email',
                'enabled' => $email->isEnabled(),
                'configured' => $email->isConfigured(),
                'icon' => 'fas fa-envelope',
                'color' => '#1976d2',
                'config_key' => 'MAIL_ENABLED',
            ],
            [
                'name' => 'WhatsApp',
                'type' => 'whatsapp',
                'enabled' => $whatsapp->isEnabled(),
                'configured' => $whatsapp->isConfigured(),
                'icon' => 'fab fa-whatsapp',
                'color' => '#25d366',
                'config_key' => 'WHATSAPP_ENABLED',
            ],
        ];

        return view('admin.integrations.index', compact('integrations'));
    }

    /**
     * Test email integration
     */
    public function testEmail(Request $request)
    {
        $email = app(EmailService::class);
        $testEmail = $request->input('email');

        $result = $email->test($testEmail);

        return response()->json($result);
    }

    /**
     * Test WhatsApp integration
     */
    public function testWhatsApp(Request $request)
    {
        $whatsapp = app(WhatsAppService::class);

        $result = $whatsapp->test();

        // Optionally send a test message
        if ($result['success'] && $request->has('phone')) {
            $sent = $whatsapp->send($request->input('phone'), 'Test message from AMTAR System');
            $result['message_sent'] = $sent;
        }

        return response()->json($result);
    }
}
