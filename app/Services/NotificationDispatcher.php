<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\EmailTemplate;
use App\Models\MessageTemplate;
use App\Services\Integrations\WhatsAppService;
use App\Services\Integrations\EmailService;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    protected EmailService $emailService;
    protected WhatsAppService $whatsAppService;

    public function __construct()
    {
        $this->emailService = app(EmailService::class);
        $this->whatsAppService = app(WhatsAppService::class);
    }

    /**
     * Send task assignment notification
     */
    public function taskAssigned(Task $task): array
    {
        $results = [];
        $assignee = $task->assignedTo;

        if (!$assignee) {
            return $results;
        }

        $data = [
            'assignee_name' => $assignee->name,
            'task_title' => $task->title,
            'project_name' => $task->project?->name ?? 'N/A',
            'priority' => $task->priority ?? 'Normal',
            'due_date' => $task->due_date?->format('Y-m-d') ?? 'Not set',
            'task_description' => $task->description ?? '',
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $assignee->email) {
            $results['email'] = $this->sendEmailTemplate($assignee->email, 'task-assigned', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $assignee->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($assignee->phone, 'whatsapp-task-assigned', $data);
        }

        return $results;
    }

    /**
     * Send task completed notification
     */
    public function taskCompleted(Task $task): array
    {
        $results = [];
        $project = $task->project;

        if (!$project || !$project->projectManager) {
            return $results;
        }

        $manager = $project->projectManager;
        $completedBy = $task->assignedTo;

        $data = [
            'recipient_name' => $manager->name,
            'task_title' => $task->title,
            'project_name' => $project->name,
            'completed_by' => $completedBy?->name ?? 'Unknown',
            'completion_date' => now()->format('Y-m-d'),
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $manager->email) {
            $results['email'] = $this->sendEmailTemplate($manager->email, 'task-completed', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $manager->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($manager->phone, 'whatsapp-task-completed', $data);
        }

        return $results;
    }

    /**
     * Send task due reminder
     */
    public function taskDueReminder(Task $task, int $daysRemaining): array
    {
        $results = [];
        $assignee = $task->assignedTo;

        if (!$assignee) {
            return $results;
        }

        $data = [
            'assignee_name' => $assignee->name,
            'task_title' => $task->title,
            'project_name' => $task->project?->name ?? 'N/A',
            'due_date' => $task->due_date?->format('Y-m-d') ?? 'Not set',
            'days_remaining' => $daysRemaining,
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $assignee->email) {
            $results['email'] = $this->sendEmailTemplate($assignee->email, 'task-due-reminder', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $assignee->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($assignee->phone, 'whatsapp-task-reminder', $data);
        }

        return $results;
    }

    /**
     * Send project created notification to client
     */
    public function projectCreated(Project $project): array
    {
        $results = [];
        $client = $project->client;

        if (!$client) {
            return $results;
        }

        $data = [
            'client_name' => $client->name,
            'project_name' => $project->name,
            'project_code' => $project->code ?? $project->id,
            'start_date' => $project->start_date?->format('Y-m-d') ?? 'TBD',
            'project_manager' => $project->projectManager?->name ?? 'TBD',
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $client->email) {
            $results['email'] = $this->sendEmailTemplate($client->email, 'project-created', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $client->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($client->phone, 'whatsapp-project-created', $data);
        }

        return $results;
    }

    /**
     * Send project status update notification
     */
    public function projectStatusUpdate(Project $project, string $updateMessage = ''): array
    {
        $results = [];
        $client = $project->client;

        if (!$client) {
            return $results;
        }

        $data = [
            'client_name' => $client->name,
            'project_name' => $project->name,
            'status' => $project->status ?? 'In Progress',
            'progress' => $project->progress ?? 0,
            'update_message' => $updateMessage,
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $client->email) {
            $results['email'] = $this->sendEmailTemplate($client->email, 'project-status-update', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $client->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($client->phone, 'whatsapp-project-update', $data);
        }

        return $results;
    }

    /**
     * Send project completed notification
     */
    public function projectCompleted(Project $project): array
    {
        $results = [];
        $client = $project->client;

        if (!$client) {
            return $results;
        }

        $data = [
            'client_name' => $client->name,
            'project_name' => $project->name,
            'project_code' => $project->code ?? $project->id,
            'completion_date' => now()->format('Y-m-d'),
            'total_milestones' => $project->milestones()->count(),
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $client->email) {
            $results['email'] = $this->sendEmailTemplate($client->email, 'project-completed', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $client->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($client->phone, 'whatsapp-project-completed', $data);
        }

        return $results;
    }

    /**
     * Send milestone completed notification
     */
    public function milestoneCompleted(Milestone $milestone): array
    {
        $results = [];
        $project = $milestone->project;
        $client = $project?->client;

        if (!$client) {
            return $results;
        }

        $data = [
            'client_name' => $client->name,
            'milestone_name' => $milestone->name,
            'project_name' => $project->name,
            'completion_date' => now()->format('Y-m-d'),
        ];

        // Send Email
        if ($this->emailService->isEnabled() && $client->email) {
            $results['email'] = $this->sendEmailTemplate($client->email, 'milestone-completed', $data);
        }

        // Send WhatsApp
        if ($this->whatsAppService->isEnabled() && $client->phone) {
            $results['whatsapp'] = $this->sendWhatsAppTemplate($client->phone, 'whatsapp-milestone-completed', $data);
        }

        return $results;
    }

    /**
     * Send email using template
     */
    protected function sendEmailTemplate(string $email, string $templateSlug, array $data): bool
    {
        try {
            $template = EmailTemplate::where('slug', $templateSlug)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning("Email template not found: {$templateSlug}");
                return false;
            }

            $subject = $this->replacePlaceholders($template->subject, $data);
            $body = $this->replacePlaceholders($template->body, $data);

            return $this->emailService->send($email, $body, ['subject' => $subject]);
        } catch (\Exception $e) {
            Log::error("Email notification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send WhatsApp message using template
     */
    protected function sendWhatsAppTemplate(string $phone, string $templateSlug, array $data): bool
    {
        try {
            $template = MessageTemplate::where('slug', $templateSlug)
                ->where('channel', 'whatsapp')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning("WhatsApp template not found: {$templateSlug}");
                return false;
            }

            $message = $this->replacePlaceholders($template->content, $data);

            return $this->whatsAppService->send($phone, $message);
        } catch (\Exception $e) {
            Log::error("WhatsApp notification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Replace placeholders in template
     */
    protected function replacePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
        }
        return $content;
    }

    /**
     * Send custom notification
     */
    public function sendCustom(string $recipient, string $message, string $channel = 'email', array $options = []): bool
    {
        return match($channel) {
            'email' => $this->emailService->isEnabled()
                ? $this->emailService->send($recipient, $message, $options)
                : false,
            'whatsapp' => $this->whatsAppService->isEnabled()
                ? $this->whatsAppService->send($recipient, $message)
                : false,
            default => false,
        };
    }
}
