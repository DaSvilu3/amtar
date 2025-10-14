<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Branding Settings
            [
                'key' => 'app_name',
                'value' => 'Amtar Engineering Consultancy',
                'type' => 'text',
                'group' => 'branding',
                'description' => 'Application name displayed in the admin panel',
            ],
            [
                'key' => 'app_logo',
                'value' => '/logo.jpg',
                'type' => 'image',
                'group' => 'branding',
                'description' => 'Application logo path',
            ],
            [
                'key' => 'company_name',
                'value' => 'Amtar Engineering Consultancy',
                'type' => 'text',
                'group' => 'branding',
                'description' => 'Official company name',
            ],
            [
                'key' => 'tagline',
                'value' => 'Engineering Excellence & Innovation',
                'type' => 'text',
                'group' => 'branding',
                'description' => 'Company tagline or slogan',
            ],

            // Contact Settings
            [
                'key' => 'contact_email',
                'value' => 'info@amtar.om',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Primary contact email',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+968 1234 5678',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Primary contact phone',
            ],
            [
                'key' => 'contact_address',
                'value' => 'Muscat, Sultanate of Oman',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Company address',
            ],

            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Amtar Engineering',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Default sender name for emails',
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@amtar.om',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Default sender email address',
            ],

            // System Settings
            [
                'key' => 'timezone',
                'value' => 'Asia/Muscat',
                'type' => 'text',
                'group' => 'system',
                'description' => 'Application timezone',
            ],
            [
                'key' => 'date_format',
                'value' => 'd/m/Y',
                'type' => 'text',
                'group' => 'system',
                'description' => 'Date display format',
            ],
            [
                'key' => 'currency',
                'value' => 'OMR',
                'type' => 'text',
                'group' => 'system',
                'description' => 'Default currency code',
            ],
            [
                'key' => 'items_per_page',
                'value' => '20',
                'type' => 'number',
                'group' => 'system',
                'description' => 'Number of items to display per page',
            ],

            // Notification Settings
            [
                'key' => 'notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable/disable system notifications',
            ],
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable/disable email notifications',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
