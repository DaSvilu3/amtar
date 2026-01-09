<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Milestone $milestone
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'milestone_completed',
            'title' => 'Milestone Completed',
            'message' => "Milestone \"{$this->milestone->title}\" has been completed for project {$this->milestone->project?->name}",
            'milestone_id' => $this->milestone->id,
            'milestone_title' => $this->milestone->title,
            'project_id' => $this->milestone->project_id,
            'project_name' => $this->milestone->project?->name,
            'url' => route('admin.milestones.show', $this->milestone),
            'icon' => 'fa-flag-checkered',
            'color' => 'success',
        ];
    }
}
