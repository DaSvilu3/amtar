<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // Project Related
            [
                'name' => 'Project Created',
                'slug' => 'project-created',
                'subject' => 'New Project: {{project_name}}',
                'body' => "Dear {{client_name}},\n\nWe are pleased to inform you that your project \"{{project_name}}\" has been created in our system.\n\nProject Details:\n- Project Code: {{project_code}}\n- Start Date: {{start_date}}\n- Project Manager: {{project_manager}}\n\nWe will keep you updated on the progress.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['client_name', 'project_name', 'project_code', 'start_date', 'project_manager'],
                'category' => 'project',
                'is_active' => true,
            ],
            [
                'name' => 'Project Status Update',
                'slug' => 'project-status-update',
                'subject' => 'Project Update: {{project_name}}',
                'body' => "Dear {{client_name}},\n\nWe would like to update you on the progress of your project.\n\nProject: {{project_name}}\nStatus: {{status}}\nProgress: {{progress}}%\n\n{{update_message}}\n\nIf you have any questions, please don't hesitate to contact us.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['client_name', 'project_name', 'status', 'progress', 'update_message'],
                'category' => 'project',
                'is_active' => true,
            ],
            [
                'name' => 'Project Completed',
                'slug' => 'project-completed',
                'subject' => 'Project Completed: {{project_name}}',
                'body' => "Dear {{client_name}},\n\nWe are delighted to inform you that your project \"{{project_name}}\" has been successfully completed.\n\nProject Summary:\n- Project Code: {{project_code}}\n- Completion Date: {{completion_date}}\n- Total Milestones: {{total_milestones}}\n\nThank you for choosing AMTAR Engineering. We look forward to working with you again.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['client_name', 'project_name', 'project_code', 'completion_date', 'total_milestones'],
                'category' => 'project',
                'is_active' => true,
            ],

            // Task Related
            [
                'name' => 'Task Assigned',
                'slug' => 'task-assigned',
                'subject' => 'New Task Assigned: {{task_title}}',
                'body' => "Dear {{assignee_name}},\n\nA new task has been assigned to you.\n\nTask Details:\n- Title: {{task_title}}\n- Project: {{project_name}}\n- Priority: {{priority}}\n- Due Date: {{due_date}}\n\nDescription:\n{{task_description}}\n\nPlease login to the system to view more details.\n\nBest regards,\nAMTAR Engineering System",
                'variables' => ['assignee_name', 'task_title', 'project_name', 'priority', 'due_date', 'task_description'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Task Due Reminder',
                'slug' => 'task-due-reminder',
                'subject' => 'Reminder: Task Due Soon - {{task_title}}',
                'body' => "Dear {{assignee_name}},\n\nThis is a reminder that the following task is due soon.\n\nTask: {{task_title}}\nProject: {{project_name}}\nDue Date: {{due_date}}\nDays Remaining: {{days_remaining}}\n\nPlease ensure the task is completed on time.\n\nBest regards,\nAMTAR Engineering System",
                'variables' => ['assignee_name', 'task_title', 'project_name', 'due_date', 'days_remaining'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Task Completed',
                'slug' => 'task-completed',
                'subject' => 'Task Completed: {{task_title}}',
                'body' => "Dear {{recipient_name}},\n\nThe following task has been marked as completed.\n\nTask: {{task_title}}\nProject: {{project_name}}\nCompleted By: {{completed_by}}\nCompletion Date: {{completion_date}}\n\nBest regards,\nAMTAR Engineering System",
                'variables' => ['recipient_name', 'task_title', 'project_name', 'completed_by', 'completion_date'],
                'category' => 'task',
                'is_active' => true,
            ],

            // Milestone Related
            [
                'name' => 'Milestone Completed',
                'slug' => 'milestone-completed',
                'subject' => 'Milestone Achieved: {{milestone_name}}',
                'body' => "Dear {{client_name}},\n\nWe are pleased to inform you that a milestone has been achieved in your project.\n\nMilestone: {{milestone_name}}\nProject: {{project_name}}\nCompletion Date: {{completion_date}}\n\nThis brings us one step closer to project completion.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['client_name', 'milestone_name', 'project_name', 'completion_date'],
                'category' => 'milestone',
                'is_active' => true,
            ],

            // Contract Related
            [
                'name' => 'Contract Created',
                'slug' => 'contract-created',
                'subject' => 'New Contract: {{contract_number}}',
                'body' => "Dear {{client_name}},\n\nA new contract has been created for your project.\n\nContract Details:\n- Contract Number: {{contract_number}}\n- Project: {{project_name}}\n- Value: {{contract_value}} OMR\n- Start Date: {{start_date}}\n- End Date: {{end_date}}\n\nPlease review the contract details and contact us if you have any questions.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['client_name', 'contract_number', 'project_name', 'contract_value', 'start_date', 'end_date'],
                'category' => 'contract',
                'is_active' => true,
            ],

            // Document Related
            [
                'name' => 'Document Uploaded',
                'slug' => 'document-uploaded',
                'subject' => 'New Document: {{document_name}}',
                'body' => "Dear {{recipient_name}},\n\nA new document has been uploaded to your project.\n\nDocument: {{document_name}}\nProject: {{project_name}}\nUploaded By: {{uploaded_by}}\nDate: {{upload_date}}\n\nYou can view the document by logging into your account.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['recipient_name', 'document_name', 'project_name', 'uploaded_by', 'upload_date'],
                'category' => 'document',
                'is_active' => true,
            ],

            // Welcome & Account
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome',
                'subject' => 'Welcome to AMTAR Engineering',
                'body' => "Dear {{user_name}},\n\nWelcome to AMTAR Engineering & Design Consultancy!\n\nYour account has been created successfully. You can now access your project information and documents through our client portal.\n\nLogin URL: {{login_url}}\nEmail: {{user_email}}\n\nIf you have any questions, please contact us at info@amtar.om\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['user_name', 'login_url', 'user_email'],
                'category' => 'account',
                'is_active' => true,
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password-reset',
                'subject' => 'Reset Your Password - AMTAR',
                'body' => "Dear {{user_name}},\n\nWe received a request to reset your password.\n\nClick the link below to reset your password:\n{{reset_link}}\n\nThis link will expire in {{expiry_time}} minutes.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nAMTAR Engineering Team",
                'variables' => ['user_name', 'reset_link', 'expiry_time'],
                'category' => 'account',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
