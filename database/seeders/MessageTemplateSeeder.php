<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // WhatsApp Templates - Project Updates
            [
                'name' => 'Project Created - WhatsApp',
                'slug' => 'whatsapp-project-created',
                'content' => "السلام عليكم {{client_name}}\n\nنود إعلامكم بأنه تم إنشاء مشروعكم في نظامنا.\n\n*تفاصيل المشروع:*\nاسم المشروع: {{project_name}}\nرقم المشروع: {{project_code}}\nمدير المشروع: {{project_manager}}\n\nسنبقيكم على اطلاع بمستجدات المشروع.\n\nمع تحيات\n*AMTAR Engineering*",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'project_name', 'project_code', 'project_manager'],
                'category' => 'project',
                'is_active' => true,
            ],
            [
                'name' => 'Project Status Update - WhatsApp',
                'slug' => 'whatsapp-project-update',
                'content' => "السلام عليكم {{client_name}}\n\n*تحديث المشروع*\n\nالمشروع: {{project_name}}\nالحالة: {{status}}\nنسبة الإنجاز: {{progress}}%\n\n{{update_message}}\n\nمع تحيات\n*AMTAR Engineering*",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'project_name', 'status', 'progress', 'update_message'],
                'category' => 'project',
                'is_active' => true,
            ],
            [
                'name' => 'Project Completed - WhatsApp',
                'slug' => 'whatsapp-project-completed',
                'content' => "السلام عليكم {{client_name}}\n\n*مبروك!* تم إنجاز مشروعكم بنجاح.\n\nالمشروع: {{project_name}}\nتاريخ الإنجاز: {{completion_date}}\n\nشكراً لثقتكم بـ AMTAR Engineering\n\nمع تحيات\n*AMTAR Engineering*",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'project_name', 'completion_date'],
                'category' => 'project',
                'is_active' => true,
            ],

            // WhatsApp Templates - Tasks
            [
                'name' => 'Task Assigned - WhatsApp',
                'slug' => 'whatsapp-task-assigned',
                'content' => "مرحباً {{assignee_name}}\n\n*مهمة جديدة*\n\nالمهمة: {{task_title}}\nالمشروع: {{project_name}}\nالأولوية: {{priority}}\nتاريخ التسليم: {{due_date}}\n\nيرجى الدخول للنظام لمزيد من التفاصيل.",
                'channel' => 'whatsapp',
                'variables' => ['assignee_name', 'task_title', 'project_name', 'priority', 'due_date'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Task Due Reminder - WhatsApp',
                'slug' => 'whatsapp-task-reminder',
                'content' => "تذكير {{assignee_name}}\n\n*مهمة مستحقة قريباً*\n\nالمهمة: {{task_title}}\nتاريخ التسليم: {{due_date}}\nالأيام المتبقية: {{days_remaining}}\n\nيرجى إنجاز المهمة في الوقت المحدد.",
                'channel' => 'whatsapp',
                'variables' => ['assignee_name', 'task_title', 'due_date', 'days_remaining'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Task Completed - WhatsApp',
                'slug' => 'whatsapp-task-completed',
                'content' => "{{recipient_name}}\n\n*تم إنجاز المهمة*\n\nالمهمة: {{task_title}}\nالمشروع: {{project_name}}\nأنجزت بواسطة: {{completed_by}}",
                'channel' => 'whatsapp',
                'variables' => ['recipient_name', 'task_title', 'project_name', 'completed_by'],
                'category' => 'task',
                'is_active' => true,
            ],

            // WhatsApp Templates - Milestones
            [
                'name' => 'Milestone Completed - WhatsApp',
                'slug' => 'whatsapp-milestone-completed',
                'content' => "السلام عليكم {{client_name}}\n\n*تم إنجاز مرحلة*\n\nالمرحلة: {{milestone_name}}\nالمشروع: {{project_name}}\nتاريخ الإنجاز: {{completion_date}}\n\nنقترب من إتمام المشروع.\n\nمع تحيات\n*AMTAR Engineering*",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'milestone_name', 'project_name', 'completion_date'],
                'category' => 'milestone',
                'is_active' => true,
            ],

            // WhatsApp Templates - Documents
            [
                'name' => 'Document Ready - WhatsApp',
                'slug' => 'whatsapp-document-ready',
                'content' => "السلام عليكم {{client_name}}\n\n*مستند جديد*\n\nتم رفع مستند جديد لمشروعكم:\n\nالمستند: {{document_name}}\nالمشروع: {{project_name}}\n\nيمكنكم الاطلاع عليه من خلال حسابكم.\n\nمع تحيات\n*AMTAR Engineering*",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'document_name', 'project_name'],
                'category' => 'document',
                'is_active' => true,
            ],

            // WhatsApp Templates - General
            [
                'name' => 'Welcome Message - WhatsApp',
                'slug' => 'whatsapp-welcome',
                'content' => "السلام عليكم {{client_name}}\n\nأهلاً بكم في *AMTAR Engineering*\n\nتم إنشاء حسابكم بنجاح. يمكنكم متابعة مشاريعكم من خلال:\n{{portal_url}}\n\nللاستفسار: info@amtar.om\n\nمع تحياتنا",
                'channel' => 'whatsapp',
                'variables' => ['client_name', 'portal_url'],
                'category' => 'general',
                'is_active' => true,
            ],
            [
                'name' => 'Meeting Reminder - WhatsApp',
                'slug' => 'whatsapp-meeting-reminder',
                'content' => "تذكير {{recipient_name}}\n\n*موعد اجتماع*\n\nالموضوع: {{meeting_subject}}\nالتاريخ: {{meeting_date}}\nالوقت: {{meeting_time}}\nالمكان: {{meeting_location}}\n\nنتطلع لحضوركم.",
                'channel' => 'whatsapp',
                'variables' => ['recipient_name', 'meeting_subject', 'meeting_date', 'meeting_time', 'meeting_location'],
                'category' => 'general',
                'is_active' => true,
            ],

            // SMS Templates (shorter versions)
            [
                'name' => 'Task Assigned - SMS',
                'slug' => 'sms-task-assigned',
                'content' => "AMTAR: New task assigned - {{task_title}}. Due: {{due_date}}. Login to view details.",
                'channel' => 'sms',
                'variables' => ['task_title', 'due_date'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Task Due Reminder - SMS',
                'slug' => 'sms-task-reminder',
                'content' => "AMTAR: Task {{task_title}} due in {{days_remaining}} days. Please complete on time.",
                'channel' => 'sms',
                'variables' => ['task_title', 'days_remaining'],
                'category' => 'task',
                'is_active' => true,
            ],
            [
                'name' => 'Project Update - SMS',
                'slug' => 'sms-project-update',
                'content' => "AMTAR: Project {{project_name}} is now {{progress}}% complete. Status: {{status}}",
                'channel' => 'sms',
                'variables' => ['project_name', 'progress', 'status'],
                'category' => 'project',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::create($template);
        }
    }
}
