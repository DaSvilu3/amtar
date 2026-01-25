<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    private string $baseUrl = 'https://api.ultramsg.com';
    private ?string $instanceId;
    private ?string $token;
    private bool $enabled;

    public function __construct()
    {
        $this->instanceId = config('services.whatsapp.instance_id');
        $this->token = config('services.whatsapp.token');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * Check if WhatsApp notifications are enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && $this->isConfigured();
    }

    /**
     * Check if WhatsApp is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->instanceId) && !empty($this->token);
    }

    /**
     * Send WhatsApp message
     */
    public function send(string $recipient, string $message): bool
    {
        if (!$this->isEnabled()) {
            Log::info('WhatsApp notifications are disabled');
            return false;
        }

        try {
            $phone = $this->formatPhoneNumber($recipient);
            $response = $this->sendWhatsAppMessage($phone, $message);
            return $response['success'] ?? false;
        } catch (Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send WhatsApp message to multiple recipients
     */
    public function sendBulk(array $recipients, string $message): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        if (!$this->isEnabled()) {
            return $results;
        }

        foreach ($recipients as $recipient) {
            if ($this->send($recipient, $message)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $recipient;
            }
        }

        return $results;
    }

    /**
     * Send WhatsApp message using template
     */
    public function sendTemplate(string $recipient, string $templateName, array $params): bool
    {
        $template = \App\Models\MessageTemplate::where('slug', $templateName)
            ->where('type', 'whatsapp')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::warning("WhatsApp template not found: {$templateName}");
            return false;
        }

        $message = $this->replaceTemplatePlaceholders($template->content, $params);
        return $this->send($recipient, $message);
    }

    /**
     * Replace template placeholders with actual values
     */
    private function replaceTemplatePlaceholders(string $content, array $params): string
    {
        foreach ($params as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    /**
     * Format phone number for WhatsApp (Omani numbers)
     */
    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        $phone = ltrim($phone, '+');

        // If 8 digits, add Oman country code
        if (preg_match('/^\d{8}$/', $phone)) {
            $phone = '968' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Send WhatsApp message via UltraMsg API
     */
    protected function sendWhatsAppMessage(string $phone, string $body): array
    {
        $url = "{$this->baseUrl}/{$this->instanceId}/messages/chat";

        $response = Http::asForm()
            ->timeout(30)
            ->post($url, [
                'token' => $this->token,
                'to' => $phone,
                'body' => $body
            ]);

        if ($response->failed()) {
            Log::error("UltraMsg API error: " . $response->body());
            throw new Exception("Failed to send WhatsApp message: " . $response->body());
        }

        $data = $response->json();

        if (isset($data['error'])) {
            throw new Exception("UltraMsg error: " . $data['error']);
        }

        return ['success' => true, 'data' => $data];
    }

    /**
     * Send image via WhatsApp
     */
    public function sendImage(string $recipient, string $imageUrl, string $caption = ''): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $phone = $this->formatPhoneNumber($recipient);
            $url = "{$this->baseUrl}/{$this->instanceId}/messages/image";

            $response = Http::asForm()
                ->timeout(30)
                ->post($url, [
                    'token' => $this->token,
                    'to' => $phone,
                    'image' => $imageUrl,
                    'caption' => $caption
                ]);

            return !$response->failed();
        } catch (Exception $e) {
            Log::error('WhatsApp image send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send document via WhatsApp
     */
    public function sendDocument(string $recipient, string $documentUrl, string $filename = ''): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $phone = $this->formatPhoneNumber($recipient);
            $url = "{$this->baseUrl}/{$this->instanceId}/messages/document";

            $response = Http::asForm()
                ->timeout(30)
                ->post($url, [
                    'token' => $this->token,
                    'to' => $phone,
                    'document' => $documentUrl,
                    'filename' => $filename
                ]);

            return !$response->failed();
        } catch (Exception $e) {
            Log::error('WhatsApp document send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test WhatsApp connection
     */
    public function test(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WhatsApp is not configured. Set WHATSAPP_INSTANCE_ID and WHATSAPP_TOKEN in .env'
            ];
        }

        try {
            $url = "{$this->baseUrl}/{$this->instanceId}/instance/status";

            $response = Http::asForm()
                ->timeout(30)
                ->get($url, ['token' => $this->token]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'message' => 'Failed to connect to UltraMsg API: ' . $response->body()
                ];
            }

            return [
                'success' => true,
                'message' => 'WhatsApp connection successful',
                'status' => $response->json()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'WhatsApp test failed: ' . $e->getMessage()
            ];
        }
    }
}
