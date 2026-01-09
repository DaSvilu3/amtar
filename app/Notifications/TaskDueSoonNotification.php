<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $urgency = $this->daysRemaining <= 1 ? 'danger' : ($this->daysRemaining <= 3 ? 'warning' : 'info');
        $title = $this->daysRemaining === 0 ? 'Task Due Today' :
                 ($this->daysRemaining === 1 ? 'Task Due Tomorrow' : "Task Due in {$this->daysRemaining} Days");

        return [
            'type' => 'task_due_soon',
            'title' => $title,
            'message' => "Task \"{$this->task->title}\" is due " .
                        ($this->daysRemaining === 0 ? 'today' :
                        ($this->daysRemaining === 1 ? 'tomorrow' : "in {$this->daysRemaining} days")),
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project?->name,
            'due_date' => $this->task->due_date?->format('Y-m-d'),
            'days_remaining' => $this->daysRemaining,
            'url' => route('admin.tasks.show', $this->task),
            'icon' => 'fa-clock',
            'color' => $urgency,
        ];
    }
}
