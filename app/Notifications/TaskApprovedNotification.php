<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $approvedBy,
        public ?string $feedback = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_approved',
            'title' => 'Task Approved',
            'message' => "Your task \"{$this->task->title}\" has been approved by {$this->approvedBy}",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project?->name,
            'approved_by' => $this->approvedBy,
            'feedback' => $this->feedback,
            'url' => route('admin.tasks.show', $this->task),
            'icon' => 'fa-check-circle',
            'color' => 'success',
        ];
    }
}
