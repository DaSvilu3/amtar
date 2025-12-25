<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Milestone;
use App\Models\UserCapacity;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TaskAssignmentService
{
    /**
     * Find the best consultant to assign a task to based on skills, availability, and workload.
     */
    public function findBestAssignee(Task $task, array $options = []): ?User
    {
        $candidates = $this->getCandidates($task, $options);

        if ($candidates->isEmpty()) {
            return null;
        }

        // Score each candidate
        $scoredCandidates = $candidates->map(function ($user) use ($task, $options) {
            return [
                'user' => $user,
                'score' => $this->calculateAssignmentScore($user, $task, $options),
            ];
        });

        // Return the user with the highest score
        $best = $scoredCandidates->sortByDesc('score')->first();

        return $best['user'];
    }

    /**
     * Get candidate users who could potentially be assigned this task.
     */
    public function getCandidates(Task $task, array $options = []): Collection
    {
        $query = User::where('is_active', true);

        // Filter by required skills from task template
        if ($task->taskTemplate && !empty($task->taskTemplate->required_skills)) {
            $requiredSkills = $task->taskTemplate->required_skills;
            $query->whereHas('skills', function ($q) use ($requiredSkills) {
                $q->whereIn('skills.id', $requiredSkills);
            }, '>=', count($requiredSkills));
        }

        // Filter by service stage specialization
        if ($task->projectService && $task->projectService->service_stage_id) {
            $stageId = $task->projectService->service_stage_id;
            $query->whereHas('serviceStages', function ($q) use ($stageId) {
                $q->where('service_stages.id', $stageId);
            });

            // Filter by expertise level if required
            if ($task->taskTemplate && $task->taskTemplate->required_expertise_level) {
                $requiredLevel = $task->taskTemplate->required_expertise_level;
                $levels = $this->getExpertiseLevelHierarchy($requiredLevel);
                $query->whereHas('serviceStages', function ($q) use ($stageId, $levels) {
                    $q->where('service_stages.id', $stageId)
                        ->whereIn('user_service_stage.expertise_level', $levels);
                });
            }
        }

        // Exclude users already at capacity
        if (!($options['ignore_capacity'] ?? false)) {
            $minHours = $task->estimated_hours ?? 1;
            $weekStart = ($task->start_date ?? now())->startOfWeek();

            $query->where(function ($q) use ($minHours, $weekStart) {
                $q->whereDoesntHave('capacities', function ($cq) use ($weekStart) {
                    $cq->where('week_start', $weekStart);
                })->orWhereHas('capacities', function ($cq) use ($minHours, $weekStart) {
                    $cq->where('week_start', $weekStart)
                        ->whereRaw('total_hours - allocated_hours >= ?', [$minHours]);
                });
            });
        }

        // Exclude specific users if provided
        if (!empty($options['exclude_users'])) {
            $query->whereNotIn('id', $options['exclude_users']);
        }

        return $query->get();
    }

    /**
     * Calculate assignment score for a user (higher is better).
     */
    protected function calculateAssignmentScore(User $user, Task $task, array $options = []): float
    {
        $score = 0;
        $weights = $options['weights'] ?? [
            'skill_match' => 30,
            'expertise_level' => 25,
            'availability' => 20,
            'workload' => 15,
            'experience' => 10,
        ];

        // Skill match score
        $score += $this->calculateSkillMatchScore($user, $task) * $weights['skill_match'];

        // Expertise level score
        $score += $this->calculateExpertiseLevelScore($user, $task) * $weights['expertise_level'];

        // Availability score (more available hours = higher score)
        $score += $this->calculateAvailabilityScore($user, $task) * $weights['availability'];

        // Workload balance score (less current workload = higher score)
        $score += $this->calculateWorkloadScore($user) * $weights['workload'];

        // Experience score (years of experience in relevant skills)
        $score += $this->calculateExperienceScore($user, $task) * $weights['experience'];

        return $score;
    }

    /**
     * Calculate skill match score (0-1).
     */
    protected function calculateSkillMatchScore(User $user, Task $task): float
    {
        if (!$task->taskTemplate || empty($task->taskTemplate->required_skills)) {
            return 1.0; // No specific skills required
        }

        $requiredSkills = $task->taskTemplate->required_skills;
        $userSkills = $user->skills()->whereIn('skills.id', $requiredSkills)->get();

        if ($userSkills->isEmpty()) {
            return 0;
        }

        // Calculate based on proficiency levels
        $proficiencyScores = ['beginner' => 0.25, 'intermediate' => 0.5, 'advanced' => 0.75, 'expert' => 1.0];
        $totalScore = 0;

        foreach ($userSkills as $skill) {
            $totalScore += $proficiencyScores[$skill->pivot->proficiency_level] ?? 0.5;
        }

        return $totalScore / count($requiredSkills);
    }

    /**
     * Calculate expertise level score (0-1).
     */
    protected function calculateExpertiseLevelScore(User $user, Task $task): float
    {
        if (!$task->projectService || !$task->projectService->service_stage_id) {
            return 0.5;
        }

        $stageId = $task->projectService->service_stage_id;
        $userStage = $user->serviceStages()->where('service_stages.id', $stageId)->first();

        if (!$userStage) {
            return 0;
        }

        $levelScores = ['junior' => 0.25, 'mid' => 0.5, 'senior' => 0.75, 'lead' => 1.0];
        $userLevel = $userStage->pivot->expertise_level;
        $requiredLevel = $task->taskTemplate?->required_expertise_level;

        if (!$requiredLevel) {
            return $levelScores[$userLevel] ?? 0.5;
        }

        // Score based on how well user level matches required level
        $userLevelValue = $levelScores[$userLevel] ?? 0.5;
        $requiredLevelValue = $levelScores[$requiredLevel] ?? 0.5;

        if ($userLevelValue >= $requiredLevelValue) {
            return 1.0; // Meets or exceeds requirement
        }

        return $userLevelValue / $requiredLevelValue;
    }

    /**
     * Calculate availability score (0-1).
     */
    protected function calculateAvailabilityScore(User $user, Task $task): float
    {
        $weekStart = ($task->start_date ?? now())->startOfWeek();
        $capacity = UserCapacity::where('user_id', $user->id)
            ->where('week_start', $weekStart)
            ->first();

        if (!$capacity) {
            return 1.0; // No capacity record means fully available
        }

        $availableHours = max(0, $capacity->total_hours - $capacity->allocated_hours);
        $requiredHours = $task->estimated_hours ?? 1;

        if ($availableHours >= $requiredHours * 2) {
            return 1.0;
        }

        if ($availableHours >= $requiredHours) {
            return 0.75;
        }

        if ($availableHours > 0) {
            return 0.5;
        }

        return 0;
    }

    /**
     * Calculate workload balance score (0-1).
     */
    protected function calculateWorkloadScore(User $user): float
    {
        $currentWorkload = $user->getCurrentWorkload();
        $maxWorkload = 40; // Assuming 40 hours max per week

        if ($currentWorkload === 0) {
            return 1.0;
        }

        $utilizationRate = $currentWorkload / $maxWorkload;

        if ($utilizationRate >= 1) {
            return 0;
        }

        return 1 - $utilizationRate;
    }

    /**
     * Calculate experience score (0-1).
     */
    protected function calculateExperienceScore(User $user, Task $task): float
    {
        if (!$task->taskTemplate || empty($task->taskTemplate->required_skills)) {
            return 0.5;
        }

        $requiredSkills = $task->taskTemplate->required_skills;
        $userSkills = $user->skills()
            ->whereIn('skills.id', $requiredSkills)
            ->whereNotNull('user_skills.years_experience')
            ->get();

        if ($userSkills->isEmpty()) {
            return 0.5;
        }

        $totalExperience = $userSkills->sum('pivot.years_experience');
        $avgExperience = $totalExperience / $userSkills->count();

        // Normalize: 5+ years = 1.0
        return min(1.0, $avgExperience / 5);
    }

    /**
     * Get expertise levels that meet or exceed the required level.
     */
    protected function getExpertiseLevelHierarchy(string $requiredLevel): array
    {
        $hierarchy = ['junior', 'mid', 'senior', 'lead'];
        $index = array_search($requiredLevel, $hierarchy);

        if ($index === false) {
            return $hierarchy;
        }

        return array_slice($hierarchy, $index);
    }

    /**
     * Find the best reviewer for a task.
     */
    public function findBestReviewer(Task $task, array $options = []): ?User
    {
        $query = User::where('is_active', true);

        // Must be able to review the service stage
        if ($task->projectService && $task->projectService->service_stage_id) {
            $stageId = $task->projectService->service_stage_id;
            $query->whereHas('serviceStages', function ($q) use ($stageId) {
                $q->where('service_stages.id', $stageId)
                    ->where('user_service_stage.can_review', true);
            });
        }

        // Exclude the task assignee (can't review own work)
        if ($task->assigned_to) {
            $query->where('id', '!=', $task->assigned_to);
        }

        // Prefer reviewers with lower pending review count
        $candidates = $query->withCount(['reviewTasks' => function ($q) {
            $q->where('status', 'review')->whereNull('reviewed_at');
        }])->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        // Return reviewer with least pending reviews
        return $candidates->sortBy('review_tasks_count')->first();
    }

    /**
     * Auto-assign a task to the best available consultant.
     */
    public function autoAssign(Task $task, array $options = []): bool
    {
        $assignee = $this->findBestAssignee($task, $options);

        if (!$assignee) {
            return false;
        }

        $task->assigned_to = $assignee->id;

        // Also assign a reviewer if task requires review
        if ($task->requires_review && empty($task->reviewed_by)) {
            $reviewer = $this->findBestReviewer($task, $options);
            if ($reviewer) {
                $task->reviewed_by = $reviewer->id;
            }
        }

        // Update capacity
        if ($task->estimated_hours) {
            $weekStart = ($task->start_date ?? now())->startOfWeek();
            $capacity = UserCapacity::getOrCreateForWeek($assignee->id, Carbon::parse($weekStart));
            $capacity->allocate($task->estimated_hours);
        }

        return $task->save();
    }

    /**
     * Generate tasks from templates for a project service.
     */
    public function generateTasksFromTemplates(
        Project $project,
        ProjectService $projectService,
        ?Milestone $milestone = null,
        bool $autoAssign = true
    ): Collection {
        $templates = TaskTemplate::where('service_id', $projectService->service_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $createdTasks = collect();
        $templateToTaskMap = [];

        foreach ($templates as $template) {
            $assigneeId = null;

            if ($autoAssign) {
                // Create task first without assignment to calculate best assignee
                $task = $template->createTask($project, $projectService, $milestone);
                $this->autoAssign($task);
                $createdTasks->push($task);
            } else {
                $task = $template->createTask($project, $projectService, $milestone);
                $createdTasks->push($task);
            }

            $templateToTaskMap[$template->id] = $task->id;
        }

        // Set up task dependencies based on template dependencies
        foreach ($templates as $template) {
            if ($template->dependencies->isNotEmpty()) {
                $taskId = $templateToTaskMap[$template->id];
                $task = Task::find($taskId);

                foreach ($template->dependencies as $depTemplate) {
                    if (isset($templateToTaskMap[$depTemplate->id])) {
                        $task->dependencies()->attach($templateToTaskMap[$depTemplate->id]);
                    }
                }
            }
        }

        return $createdTasks;
    }

    /**
     * Get assignment suggestions for a task (multiple options with scores).
     */
    public function getAssignmentSuggestions(Task $task, int $limit = 5): Collection
    {
        $candidates = $this->getCandidates($task);

        return $candidates->map(function ($user) use ($task) {
            return [
                'user' => $user,
                'score' => $this->calculateAssignmentScore($user, $task),
                'available_hours' => $user->getAvailableHoursForWeek($task->start_date),
                'current_workload' => $user->getCurrentWorkload(),
                'matching_skills' => $this->getMatchingSkills($user, $task),
            ];
        })->sortByDesc('score')->take($limit)->values();
    }

    /**
     * Get matching skills between user and task requirements.
     */
    protected function getMatchingSkills(User $user, Task $task): Collection
    {
        if (!$task->taskTemplate || empty($task->taskTemplate->required_skills)) {
            return collect();
        }

        return $user->skills()
            ->whereIn('skills.id', $task->taskTemplate->required_skills)
            ->get();
    }

    /**
     * Recalculate and update capacity for all users based on current task assignments.
     */
    public function recalculateAllCapacities(Carbon $weekStart = null): void
    {
        $weekStart = $weekStart ?? now()->startOfWeek();

        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            $capacity = UserCapacity::getOrCreateForWeek($user->id, $weekStart);
            $capacity->recalculateFromTasks();
        }
    }
}
