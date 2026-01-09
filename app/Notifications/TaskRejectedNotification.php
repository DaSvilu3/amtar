<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $rejectedBy,
        public ?string $feedback = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_rejected',
            'title' => 'Task Requires Revision',
            'message' => "Your task \"{$this->task->title}\" has been rejected by {$this->rejectedBy}",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project?->name,
            'rejected_by' => $this->rejectedBy,
            'feedback' => $this->feedback,
            'url' => route('admin.tasks.show', $this->task),
            'icon' => 'fa-times-circle',
            'color' => 'danger',
        ];
    }
}
