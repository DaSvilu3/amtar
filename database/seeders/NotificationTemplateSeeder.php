<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Project Notifications
            [
                'name' => 'Project Created',
                'slug' => 'project-created',
                'subject' => 'New Project Created',
                'content' => 'Project "{{project_name}}" has been created and assigned to {{project_manager}}.',
                'type' => 'info',
                'variables' => ['project_name', 'project_manager'],
                'is_active' => true,
            ],
            [
                'name' => 'Project Status Changed',
                'slug' => 'project-status-changed',
                'subject' => 'Project Status Updated',
                'content' => 'Project "{{project_name}}" status changed from {{old_status}} to {{new_status}}.',
                'type' => 'info',
                'variables' => ['project_name', 'old_status', 'new_status'],
                'is_active' => true,
            ],
            [
                'name' => 'Project Completed',
                'slug' => 'project-completed',
                'subject' => 'Project Completed',
                'content' => 'Congratulations! Project "{{project_name}}" has been successfully completed.',
                'type' => 'success',
                'variables' => ['project_name'],
                'is_active' => true,
            ],

            // Task Notifications
            [
                'name' => 'Task Assigned',
                'slug' => 'task-assigned',
                'subject' => 'New Task Assigned',
                'content' => 'You have been assigned to task "{{task_title}}" in project "{{project_name}}". Due: {{due_date}}',
                'type' => 'info',
                'variables' => ['task_title', 'project_name', 'due_date'],
                'is_active' => true,
            ],
            [
                'name' => 'Task Due Soon',
                'slug' => 'task-due-soon',
                'subject' => 'Task Due Soon',
                'content' => 'Task "{{task_title}}" is due in {{days_remaining}} days.',
                'type' => 'warning',
                'variables' => ['task_title', 'days_remaining'],
                'is_active' => true,
            ],
            [
                'name' => 'Task Overdue',
                'slug' => 'task-overdue',
                'subject' => 'Task Overdue',
                'content' => 'Task "{{task_title}}" is {{days_overdue}} days overdue. Please complete it as soon as possible.',
                'type' => 'danger',
                'variables' => ['task_title', 'days_overdue'],
                'is_active' => true,
            ],
            [
                'name' => 'Task Completed',
                'slug' => 'task-completed',
                'subject' => 'Task Completed',
                'content' => 'Task "{{task_title}}" has been completed by {{completed_by}}.',
                'type' => 'success',
                'variables' => ['task_title', 'completed_by'],
                'is_active' => true,
            ],
            [
                'name' => 'Task Comment Added',
                'slug' => 'task-comment-added',
                'subject' => 'New Comment on Task',
                'content' => '{{commenter_name}} commented on task "{{task_title}}": "{{comment_preview}}"',
                'type' => 'info',
                'variables' => ['commenter_name', 'task_title', 'comment_preview'],
                'is_active' => true,
            ],

            // Milestone Notifications
            [
                'name' => 'Milestone Due Soon',
                'slug' => 'milestone-due-soon',
                'subject' => 'Milestone Due Soon',
                'content' => 'Milestone "{{milestone_name}}" for project "{{project_name}}" is due in {{days_remaining}} days.',
                'type' => 'warning',
                'variables' => ['milestone_name', 'project_name', 'days_remaining'],
                'is_active' => true,
            ],
            [
                'name' => 'Milestone Completed',
                'slug' => 'milestone-completed',
                'subject' => 'Milestone Achieved',
                'content' => 'Milestone "{{milestone_name}}" has been completed for project "{{project_name}}".',
                'type' => 'success',
                'variables' => ['milestone_name', 'project_name'],
                'is_active' => true,
            ],

            // Document Notifications
            [
                'name' => 'Document Uploaded',
                'slug' => 'document-uploaded',
                'subject' => 'New Document Uploaded',
                'content' => '{{uploader_name}} uploaded "{{document_name}}" to project "{{project_name}}".',
                'type' => 'info',
                'variables' => ['uploader_name', 'document_name', 'project_name'],
                'is_active' => true,
            ],
            [
                'name' => 'Document Approved',
                'slug' => 'document-approved',
                'subject' => 'Document Approved',
                'content' => 'Document "{{document_name}}" has been approved by {{approver_name}}.',
                'type' => 'success',
                'variables' => ['document_name', 'approver_name'],
                'is_active' => true,
            ],

            // Contract Notifications
            [
                'name' => 'Contract Created',
                'slug' => 'contract-created',
                'subject' => 'New Contract Created',
                'content' => 'Contract #{{contract_number}} created for project "{{project_name}}" with value {{contract_value}} OMR.',
                'type' => 'info',
                'variables' => ['contract_number', 'project_name', 'contract_value'],
                'is_active' => true,
            ],
            [
                'name' => 'Contract Expiring',
                'slug' => 'contract-expiring',
                'subject' => 'Contract Expiring Soon',
                'content' => 'Contract #{{contract_number}} for project "{{project_name}}" expires in {{days_remaining}} days.',
                'type' => 'warning',
                'variables' => ['contract_number', 'project_name', 'days_remaining'],
                'is_active' => true,
            ],

            // System Notifications
            [
                'name' => 'System Maintenance',
                'slug' => 'system-maintenance',
                'subject' => 'Scheduled Maintenance',
                'content' => 'System maintenance scheduled for {{maintenance_date}}. The system may be unavailable during this time.',
                'type' => 'warning',
                'variables' => ['maintenance_date'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::create($template);
        }
    }
}
