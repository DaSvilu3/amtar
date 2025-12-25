<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Milestone;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::with(['services', 'milestones'])->get();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectSeeder first.');
            return;
        }

        // Get users by role
        $engineers = User::whereHas('roles', fn($q) => $q->where('slug', 'engineer'))->get();
        $projectManagers = User::whereHas('roles', fn($q) => $q->where('slug', 'project-manager'))->get();
        $admins = User::whereHas('roles', fn($q) => $q->where('slug', 'administrator'))->get();

        // Combine assignable users (engineers and PMs can be assigned)
        $assignableUsers = $engineers->merge($projectManagers);

        if ($assignableUsers->isEmpty()) {
            $this->command->warn('No assignable users found. Run UserSeeder first.');
            return;
        }

        // Task templates by category
        $taskTemplates = [
            'concept' => [
                ['title' => 'Client Requirements Documentation', 'hours' => 4, 'priority' => 'high'],
                ['title' => 'Site Analysis Report', 'hours' => 6, 'priority' => 'high'],
                ['title' => 'Mood Board Creation', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Initial Concept Sketches', 'hours' => 12, 'priority' => 'high'],
                ['title' => 'Space Planning Options', 'hours' => 10, 'priority' => 'medium'],
                ['title' => 'Material Palette Selection', 'hours' => 6, 'priority' => 'medium'],
            ],
            'design' => [
                ['title' => 'Detailed Floor Plans', 'hours' => 16, 'priority' => 'high'],
                ['title' => 'Elevation Drawings', 'hours' => 12, 'priority' => 'high'],
                ['title' => 'Ceiling Plan Design', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Lighting Layout', 'hours' => 6, 'priority' => 'medium'],
                ['title' => 'Furniture Layout', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Custom Furniture Details', 'hours' => 12, 'priority' => 'low'],
            ],
            'visualization' => [
                ['title' => '3D Model Development', 'hours' => 16, 'priority' => 'high'],
                ['title' => 'Interior Renderings - Living Areas', 'hours' => 8, 'priority' => 'high'],
                ['title' => 'Interior Renderings - Bedrooms', 'hours' => 6, 'priority' => 'medium'],
                ['title' => 'Material Board Finalization', 'hours' => 4, 'priority' => 'medium'],
                ['title' => 'Walkthrough Animation', 'hours' => 20, 'priority' => 'low'],
            ],
            'documentation' => [
                ['title' => 'Construction Drawings Package', 'hours' => 24, 'priority' => 'high'],
                ['title' => 'Material Specifications', 'hours' => 8, 'priority' => 'high'],
                ['title' => 'Finishes Schedule', 'hours' => 6, 'priority' => 'medium'],
                ['title' => 'Door & Window Schedule', 'hours' => 4, 'priority' => 'medium'],
                ['title' => 'Joinery Details', 'hours' => 12, 'priority' => 'medium'],
                ['title' => 'Bill of Quantities', 'hours' => 10, 'priority' => 'high'],
            ],
            'procurement' => [
                ['title' => 'Vendor Sourcing', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Price Comparison Analysis', 'hours' => 6, 'priority' => 'medium'],
                ['title' => 'Sample Collection & Approval', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Purchase Order Preparation', 'hours' => 4, 'priority' => 'high'],
            ],
            'supervision' => [
                ['title' => 'Site Inspection - Week 1', 'hours' => 4, 'priority' => 'high'],
                ['title' => 'Site Inspection - Week 2', 'hours' => 4, 'priority' => 'high'],
                ['title' => 'Progress Photography', 'hours' => 2, 'priority' => 'low'],
                ['title' => 'Quality Control Review', 'hours' => 6, 'priority' => 'high'],
                ['title' => 'Contractor Coordination Meeting', 'hours' => 3, 'priority' => 'medium'],
            ],
            'closeout' => [
                ['title' => 'Snagging List Preparation', 'hours' => 6, 'priority' => 'high'],
                ['title' => 'Final Inspection', 'hours' => 4, 'priority' => 'high'],
                ['title' => 'As-Built Documentation', 'hours' => 8, 'priority' => 'medium'],
                ['title' => 'Client Handover Meeting', 'hours' => 3, 'priority' => 'high'],
                ['title' => 'Warranty Documentation', 'hours' => 4, 'priority' => 'medium'],
            ],
        ];

        $totalTasks = 0;
        $statuses = ['pending', 'in_progress', 'review', 'completed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($projects as $project) {
            // Skip cancelled projects
            if ($project->status === 'cancelled') {
                continue;
            }

            $projectStart = $project->start_date ?? now();
            $projectServices = $project->services;
            $milestones = $project->milestones;
            $createdBy = $projectManagers->isNotEmpty() ? $projectManagers->random()->id : $admins->first()?->id;

            // Select task categories based on project progress
            $categoriesToUse = ['concept', 'design'];
            if ($project->progress > 25) {
                $categoriesToUse[] = 'visualization';
            }
            if ($project->progress > 40) {
                $categoriesToUse[] = 'documentation';
            }
            if ($project->progress > 60) {
                $categoriesToUse[] = 'procurement';
                $categoriesToUse[] = 'supervision';
            }
            if ($project->progress > 85) {
                $categoriesToUse[] = 'closeout';
            }

            $taskNumber = 0;

            foreach ($categoriesToUse as $category) {
                $templates = $taskTemplates[$category];
                // Take a subset of tasks from each category
                $selectedTemplates = array_slice($templates, 0, rand(2, min(4, count($templates))));

                foreach ($selectedTemplates as $template) {
                    $taskNumber++;

                    // Calculate task dates
                    $startDate = $projectStart->copy()->addDays($taskNumber * rand(3, 7));
                    $dueDate = $startDate->copy()->addDays(ceil($template['hours'] / 8) + rand(1, 5));

                    // Determine task status based on project status and due date
                    $status = 'pending';
                    $completedAt = null;
                    $reviewedAt = null;
                    $progress = 0;

                    if ($project->status === 'completed') {
                        $status = 'completed';
                        $completedAt = $dueDate->copy()->subDays(rand(0, 3));
                        $progress = 100;
                    } elseif ($project->status === 'in_progress') {
                        if ($dueDate->isPast()) {
                            // Past due tasks
                            $rand = rand(0, 100);
                            if ($rand < 60) {
                                $status = 'completed';
                                $completedAt = $dueDate->copy()->addDays(rand(-2, 3));
                                $progress = 100;
                            } elseif ($rand < 80) {
                                $status = 'in_progress';
                                $progress = rand(50, 90);
                            } else {
                                $status = 'review';
                                $progress = 100;
                            }
                        } elseif ($startDate->isPast()) {
                            // Started but not due yet
                            $rand = rand(0, 100);
                            if ($rand < 30) {
                                $status = 'in_progress';
                                $progress = rand(20, 70);
                            } elseif ($rand < 45) {
                                $status = 'review';
                                $progress = 100;
                            } elseif ($rand < 55) {
                                $status = 'completed';
                                $completedAt = now()->subDays(rand(1, 5));
                                $progress = 100;
                            }
                        }
                    }

                    // Assign to a random user
                    $assignedTo = $assignableUsers->random();

                    // Determine reviewer (for tasks requiring review)
                    $requiresReview = rand(0, 100) > 50;
                    $reviewedBy = null;
                    if ($requiresReview && $status === 'completed') {
                        $reviewedBy = $projectManagers->isNotEmpty() ? $projectManagers->random()->id : null;
                        $reviewedAt = $completedAt?->copy()->addHours(rand(2, 24));
                    } elseif ($requiresReview && $status === 'review') {
                        $reviewedBy = $projectManagers->isNotEmpty() ? $projectManagers->random()->id : null;
                    }

                    // Get a random project service if available
                    $projectService = $projectServices->isNotEmpty() ? $projectServices->random() : null;

                    // Get a milestone if available
                    $milestone = $milestones->isNotEmpty() ? $milestones->random() : null;

                    Task::create([
                        'project_id' => $project->id,
                        'project_service_id' => $projectService?->id,
                        'milestone_id' => $milestone?->id,
                        'task_template_id' => null,
                        'assigned_to' => $assignedTo->id,
                        'reviewed_by' => $reviewedBy,
                        'created_by' => $createdBy,
                        'title' => $template['title'],
                        'description' => "Task for {$project->name}: {$template['title']}. This task involves detailed work on the project deliverables.",
                        'status' => $status,
                        'priority' => $template['priority'],
                        'start_date' => $startDate,
                        'due_date' => $dueDate,
                        'completed_at' => $completedAt,
                        'reviewed_at' => $reviewedAt,
                        'review_notes' => $status === 'completed' && $reviewedBy ? 'Work reviewed and approved.' : null,
                        'estimated_hours' => $template['hours'],
                        'actual_hours' => $status === 'completed' ? $template['hours'] + rand(-2, 4) : null,
                        'progress' => $progress,
                        'requires_review' => $requiresReview,
                        'sort_order' => $taskNumber,
                    ]);

                    $totalTasks++;
                }
            }
        }

        $this->command->info("Created {$totalTasks} tasks across " . $projects->where('status', '!=', 'cancelled')->count() . " projects");
    }
}
