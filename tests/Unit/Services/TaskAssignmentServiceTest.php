<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Skill;
use App\Models\ServiceStage;
use App\Models\TaskTemplate;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\UserCapacity;
use App\Services\TaskAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskAssignmentService();
    }

    public function test_finds_best_assignee_based_on_skill_match(): void
    {
        // Create senior engineer with expert skill
        $seniorEngineer = User::factory()->create(['is_active' => true]);
        $skill = Skill::factory()->create();
        $seniorEngineer->skills()->attach($skill->id, [
            'proficiency_level' => 'expert',
            'years_experience' => 7
        ]);

        // Create junior engineer with beginner skill
        $juniorEngineer = User::factory()->create(['is_active' => true]);
        $juniorEngineer->skills()->attach($skill->id, [
            'proficiency_level' => 'beginner',
            'years_experience' => 1
        ]);

        // Create task requiring this skill
        $task = Task::factory()->create();
        $taskTemplate = TaskTemplate::factory()->create([
            'required_skills' => [$skill->id]
        ]);
        $task->update(['task_template_id' => $taskTemplate->id]);

        // Create capacity for both users
        UserCapacity::factory()->create([
            'user_id' => $seniorEngineer->id,
            'total_capacity_hours' => 40,
            'allocated_hours' => 20,
        ]);
        UserCapacity::factory()->create([
            'user_id' => $juniorEngineer->id,
            'total_capacity_hours' => 40,
            'allocated_hours' => 20,
        ]);

        $assignee = $this->service->findBestAssignee($task);

        $this->assertEquals($seniorEngineer->id, $assignee->id);
    }

    public function test_excludes_users_at_capacity(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        // User at full capacity
        UserCapacity::factory()->atCapacity()->create([
            'user_id' => $user->id,
            'total_capacity_hours' => 40,
            'allocated_hours' => 40,
            'available_hours' => 0,
        ]);

        $task = Task::factory()->create();

        $candidates = $this->service->getCandidates($task);

        $this->assertEmpty($candidates);
    }

    public function test_prioritizes_expertise_level_match(): void
    {
        $stage = ServiceStage::factory()->create();

        // Senior engineer specialized in this stage
        $seniorEngineer = User::factory()->create(['is_active' => true]);
        $seniorEngineer->serviceStages()->attach($stage->id, [
            'expertise_level' => 'senior',
            'can_review' => true
        ]);

        // Junior engineer in same stage
        $juniorEngineer = User::factory()->create(['is_active' => true]);
        $juniorEngineer->serviceStages()->attach($stage->id, [
            'expertise_level' => 'junior',
            'can_review' => false
        ]);

        // Create capacities
        UserCapacity::factory()->create(['user_id' => $seniorEngineer->id]);
        UserCapacity::factory()->create(['user_id' => $juniorEngineer->id]);

        $projectService = ProjectService::factory()->create([
            'service_stage_id' => $stage->id
        ]);
        $task = Task::factory()->create([
            'project_service_id' => $projectService->id
        ]);

        $assignee = $this->service->findBestAssignee($task);

        $this->assertEquals($seniorEngineer->id, $assignee->id);
    }

    public function test_calculates_correct_workload_score(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        UserCapacity::factory()->create([
            'user_id' => $user->id,
            'total_capacity_hours' => 40,
            'allocated_hours' => 20,
            'utilization_percentage' => 50
        ]);

        $task = Task::factory()->create();

        $score = $this->service->calculateWorkloadScore($user, $task);

        // 50% utilization should give a decent workload score
        $this->assertGreaterThan(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    public function test_handles_urgent_tasks_correctly(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        UserCapacity::factory()->create(['user_id' => $user->id]);

        $urgentTask = Task::factory()->urgent()->create();
        $normalTask = Task::factory()->create(['priority' => 'normal']);

        $urgentScore = $this->service->calculateAssignmentScore($user, $urgentTask);
        $normalScore = $this->service->calculateAssignmentScore($user, $normalTask);

        // Urgent tasks should have higher priority weighting
        $this->assertGreaterThan($normalScore, $urgentScore);
    }

    public function test_auto_assignment_updates_capacity(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $capacity = UserCapacity::factory()->create([
            'user_id' => $user->id,
            'total_capacity_hours' => 40,
            'allocated_hours' => 10,
        ]);

        $task = Task::factory()->create([
            'estimated_hours' => 8,
            'assigned_to' => null
        ]);

        $this->service->autoAssign($task, $user);

        $capacity->refresh();
        $this->assertEquals(18, $capacity->allocated_hours);
        $this->assertEquals($user->id, $task->fresh()->assigned_to);
    }

    public function test_generates_tasks_from_templates_with_dependencies(): void
    {
        $project = Project::factory()->create();
        $projectService = ProjectService::factory()->create([
            'project_id' => $project->id
        ]);

        $template1 = TaskTemplate::factory()->create(['sort_order' => 1]);
        $template2 = TaskTemplate::factory()->create(['sort_order' => 2]);

        $templates = collect([$template1, $template2]);

        $tasks = $this->service->generateTasksFromTemplates($project, $projectService, $templates);

        $this->assertCount(2, $tasks);
        $this->assertEquals($project->id, $tasks[0]->project_id);
        $this->assertEquals($projectService->id, $tasks[0]->project_service_id);
    }

    public function test_assignment_suggestions_return_top_candidates(): void
    {
        // Create 5 engineers with varying skills
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create(['is_active' => true]);
            UserCapacity::factory()->create([
                'user_id' => $user->id,
                'allocated_hours' => $i * 5, // Varying workload
            ]);
        }

        $task = Task::factory()->create();

        $suggestions = $this->service->getAssignmentSuggestions($task, 3);

        $this->assertCount(3, $suggestions);
        $this->assertArrayHasKey('user', $suggestions[0]);
        $this->assertArrayHasKey('score', $suggestions[0]);
        $this->assertArrayHasKey('reasons', $suggestions[0]);
    }

    public function test_inactive_users_are_excluded_from_candidates(): void
    {
        $inactiveUser = User::factory()->create(['is_active' => false]);
        UserCapacity::factory()->create(['user_id' => $inactiveUser->id]);

        $task = Task::factory()->create();

        $candidates = $this->service->getCandidates($task);

        $this->assertEmpty($candidates);
    }

    public function test_reviewer_assignment_selects_can_review_users(): void
    {
        $stage = ServiceStage::factory()->create();

        $reviewer = User::factory()->create(['is_active' => true]);
        $reviewer->serviceStages()->attach($stage->id, [
            'expertise_level' => 'senior',
            'can_review' => true
        ]);

        $nonReviewer = User::factory()->create(['is_active' => true]);
        $nonReviewer->serviceStages()->attach($stage->id, [
            'expertise_level' => 'junior',
            'can_review' => false
        ]);

        $projectService = ProjectService::factory()->create([
            'service_stage_id' => $stage->id
        ]);
        $task = Task::factory()->create([
            'project_service_id' => $projectService->id,
            'requires_review' => true
        ]);

        $assignedReviewer = $this->service->assignReviewer($task);

        $this->assertEquals($reviewer->id, $assignedReviewer->id);
    }
}
