<?php

namespace App\Services\Integrations;

use App\Models\Integration;
use Twilio\Rest\Client as TwilioClient;
use Exception;

class SmsService implements IntegrationServiceInterface
{
    private array $config;
    private ?Integration $integration;

    public function __construct()
    {
        $this->integration = Integration::where('type', 'sms')
            ->where('is_active', true)
            ->first();

        $this->config = $this->integration?->config ?? [];
    }

    /**
     * Send SMS via Twilio
     */
    public function send(string $recipient, string $message, array $options = []): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $client = new TwilioClient(
                $this->config['account_sid'],
                $this->config['auth_token']
            );

            $client->messages->create($recipient, [
                'from' => $this->config['from_number'],
                'body' => $message
            ]);

            // Update last sync timestamp
            $this->integration->update(['last_sync_at' => now()]);

            return true;
        } catch (Exception $e) {
            \Log::error('SMS send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS using template
     */
    public function sendTemplate(string $recipient, string $templateName, array $params): bool
    {
        // Load message template
        $template = \App\Models\MessageTemplate::where('slug', $templateName)
            ->where('type', 'sms')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return false;
        }

        // Replace template variables
        $message = $this->replaceTemplatePlaceholders($template->content, $params);

        // Ensure SMS is within character limit (160 for GSM, 70 for Unicode)
        $message = substr($message, 0, 160);

        return $this->send($recipient, $message);
    }

    /**
     * Send bulk SMS to multiple recipients
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->send($recipient, $message);
        }

        return $results;
    }

    /**
     * Replace template placeholders
     */
    private function replaceTemplatePlaceholders(string $content, array $params): string
    {
        foreach ($params as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    /**
     * Check if SMS integration is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->config['account_sid'])
            && !empty($this->config['auth_token'])
            && !empty($this->config['from_number']);
    }

    /**
     * Test SMS integration
     */
    public function test(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'SMS integration is not configured'
            ];
        }

        try {
            $client = new TwilioClient(
                $this->config['account_sid'],
                $this->config['auth_token']
            );

            // Verify credentials by getting account info
            $account = $client->api->v2010->accounts($this->config['account_sid'])->fetch();

            return [
                'success' => true,
                'message' => 'SMS integration test successful',
                'account_status' => $account->status
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SMS integration test failed: ' . $e->getMessage()
            ];
        }
    }
}
