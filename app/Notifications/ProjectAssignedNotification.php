<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public string $assignedBy
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'project_assigned',
            'title' => 'Project Assigned',
            'message' => "You have been assigned as Project Manager for: {$this->project->name}",
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'client_name' => $this->project->client?->company_name,
            'assigned_by' => $this->assignedBy,
            'url' => route('admin.projects.show', $this->project),
            'icon' => 'fa-project-diagram',
            'color' => 'primary',
        ];
    }
}
