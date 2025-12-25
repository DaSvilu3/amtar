<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ServiceStage;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Run ProjectSeeder first.');
            return;
        }

        $milestoneTemplates = [
            [
                'title' => 'Project Kickoff',
                'description' => 'Initial project meeting and requirements gathering',
                'payment_percentage' => 10.00,
                'days_from_start' => 0,
            ],
            [
                'title' => 'Concept Design Approval',
                'description' => 'Client approval of initial concept designs and mood boards',
                'payment_percentage' => 15.00,
                'days_from_start' => 14,
            ],
            [
                'title' => 'Design Development Complete',
                'description' => 'Detailed design documentation and specifications finalized',
                'payment_percentage' => 20.00,
                'days_from_start' => 45,
            ],
            [
                'title' => 'Construction Documents Ready',
                'description' => 'All construction drawings and technical documents prepared',
                'payment_percentage' => 15.00,
                'days_from_start' => 75,
            ],
            [
                'title' => 'Mid-Project Review',
                'description' => 'Progress review meeting and quality assessment',
                'payment_percentage' => 15.00,
                'days_from_start' => 100,
            ],
            [
                'title' => 'Final Installation',
                'description' => 'Completion of all installation and finishing works',
                'payment_percentage' => 15.00,
                'days_from_start' => 140,
            ],
            [
                'title' => 'Project Handover',
                'description' => 'Final inspection, snagging, and client handover',
                'payment_percentage' => 10.00,
                'days_from_start' => 160,
            ],
        ];

        $totalMilestones = 0;
        $serviceStages = ServiceStage::all()->keyBy('slug');

        foreach ($projects as $project) {
            // Skip cancelled projects
            if ($project->status === 'cancelled') {
                continue;
            }

            $projectStart = $project->start_date ?? now();
            $projectBudget = $project->budget ?? 100000;

            // Determine how many milestones based on project size
            $numMilestones = $projectBudget > 200000 ? 7 : ($projectBudget > 100000 ? 5 : 4);
            $selectedTemplates = array_slice($milestoneTemplates, 0, $numMilestones);

            foreach ($selectedTemplates as $index => $template) {
                $targetDate = $projectStart->copy()->addDays($template['days_from_start']);
                $paymentAmount = ($projectBudget * $template['payment_percentage']) / 100;

                // Determine milestone status based on project status and target date
                $status = 'pending';
                $completedAt = null;

                if ($project->status === 'completed') {
                    $status = 'completed';
                    $completedAt = $targetDate->copy()->subDays(rand(0, 5));
                } elseif ($project->status === 'in_progress') {
                    if ($targetDate->isPast()) {
                        // Past milestones are likely completed
                        if (rand(0, 100) > 20) {
                            $status = 'completed';
                            $completedAt = $targetDate->copy()->addDays(rand(-3, 5));
                        } else {
                            $status = 'overdue';
                        }
                    } elseif ($targetDate->diffInDays(now()) < 14 && $index > 0) {
                        $status = 'in_progress';
                    }
                }

                // Link to a service stage if appropriate
                $stageSlug = match($index) {
                    0 => 'briefing',
                    1 => 'concept',
                    2 => 'design_development',
                    3 => 'construction_documents',
                    4 => 'implementation',
                    5 => 'implementation',
                    6 => 'quality_handover',
                    default => null,
                };

                Milestone::create([
                    'project_id' => $project->id,
                    'service_stage_id' => isset($serviceStages[$stageSlug]) ? $serviceStages[$stageSlug]->id : null,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'target_date' => $targetDate,
                    'completed_at' => $completedAt,
                    'status' => $status,
                    'payment_percentage' => $template['payment_percentage'],
                    'payment_amount' => $paymentAmount,
                    'sort_order' => $index + 1,
                ]);

                $totalMilestones++;
            }
        }

        $this->command->info("Created {$totalMilestones} milestones across " . $projects->where('status', '!=', 'cancelled')->count() . " projects");
    }
}
