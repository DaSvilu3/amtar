<?php

namespace App\Services\Integrations;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = config('mail.enabled', true);
    }

    /**
     * Check if email notifications are enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if email is configured
     */
    public function isConfigured(): bool
    {
        return !empty(config('mail.mailers.smtp.host'))
            && !empty(config('mail.mailers.smtp.username'));
    }

    /**
     * Send plain text email
     */
    public function send(string $recipient, string $message, array $options = []): bool
    {
        if (!$this->isEnabled()) {
            Log::info('Email notifications are disabled');
            return false;
        }

        try {
            Mail::raw($message, function ($mail) use ($recipient, $options) {
                $mail->to($recipient)
                    ->subject($options['subject'] ?? 'Notification from AMTAR');

                if (!empty($options['from'])) {
                    $mail->from($options['from'], $options['from_name'] ?? config('app.name'));
                }

                if (!empty($options['cc'])) {
                    $mail->cc($options['cc']);
                }

                if (!empty($options['bcc'])) {
                    $mail->bcc($options['bcc']);
                }

                if (!empty($options['attachments'])) {
                    foreach ($options['attachments'] as $attachment) {
                        $mail->attach($attachment);
                    }
                }
            });

            return true;
        } catch (Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using template from database
     */
    public function sendTemplate(string $recipient, string $templateSlug, array $data): bool
    {
        $template = EmailTemplate::where('slug', $templateSlug)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::warning("Email template not found: {$templateSlug}");
            return false;
        }

        $subject = $this->replaceTemplatePlaceholders($template->subject, $data);
        $body = $this->replaceTemplatePlaceholders($template->body, $data);

        return $this->send($recipient, $body, ['subject' => $subject]);
    }

    /**
     * Send HTML email using a Blade view
     */
    public function sendHtml(string $recipient, string $view, array $data, array $options = []): bool
    {
        if (!$this->isEnabled()) {
            Log::info('Email notifications are disabled');
            return false;
        }

        try {
            Mail::send($view, $data, function ($mail) use ($recipient, $options) {
                $mail->to($recipient)
                    ->subject($options['subject'] ?? 'Notification from AMTAR');

                if (!empty($options['cc'])) {
                    $mail->cc($options['cc']);
                }

                if (!empty($options['bcc'])) {
                    $mail->bcc($options['bcc']);
                }

                if (!empty($options['attachments'])) {
                    foreach ($options['attachments'] as $attachment) {
                        $mail->attach($attachment);
                    }
                }
            });

            return true;
        } catch (Exception $e) {
            Log::error('HTML email send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     */
    public function sendBulk(array $recipients, string $message, array $options = []): array
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
            if ($this->send($recipient, $message, $options)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $recipient;
            }
        }

        return $results;
    }

    /**
     * Replace template placeholders
     */
    private function replaceTemplatePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    /**
     * Test email configuration
     */
    public function test(string $testEmail = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Email is not configured. Check MAIL_* settings in .env'
            ];
        }

        try {
            $recipient = $testEmail ?? config('mail.from.address');

            Mail::raw('This is a test email from AMTAR Engineering System.', function ($mail) use ($recipient) {
                $mail->to($recipient)
                    ->subject('AMTAR - Email Test');
            });

            return [
                'success' => true,
                'message' => 'Test email sent successfully to ' . $recipient
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage()
            ];
        }
    }
}
