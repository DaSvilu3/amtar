<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReviewSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $submittedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_review_submitted',
            'title' => 'Task Submitted for Review',
            'message' => "Task \"{$this->task->title}\" has been submitted for review by {$this->submittedBy}",
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project?->name,
            'submitted_by' => $this->submittedBy,
            'url' => route('admin.tasks.show', $this->task),
            'icon' => 'fa-clipboard-check',
            'color' => 'warning',
        ];
    }
}
