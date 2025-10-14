<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Integration;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            [
                'name' => 'WhatsApp Business API',
                'type' => 'whatsapp',
                'provider' => 'Twilio',
                'config' => json_encode([
                    'account_sid' => '',
                    'auth_token' => '',
                    'whatsapp_number' => '',
                    'webhook_url' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Email Service (SMTP)',
                'type' => 'email',
                'provider' => 'SMTP',
                'config' => json_encode([
                    'host' => 'smtp.gmail.com',
                    'port' => '587',
                    'username' => '',
                    'password' => '',
                    'encryption' => 'tls',
                    'from_address' => 'noreply@amtar.om',
                    'from_name' => 'Amtar Engineering',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Email Service (SendGrid)',
                'type' => 'email',
                'provider' => 'SendGrid',
                'config' => json_encode([
                    'api_key' => '',
                    'from_address' => 'noreply@amtar.om',
                    'from_name' => 'Amtar Engineering',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'SMS Service',
                'type' => 'sms',
                'provider' => 'Twilio',
                'config' => json_encode([
                    'account_sid' => '',
                    'auth_token' => '',
                    'phone_number' => '',
                ]),
                'is_active' => false,
            ],
        ];

        foreach ($integrations as $integration) {
            Integration::create($integration);
        }
    }
}
