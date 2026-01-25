<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Dispatch to role-specific dashboard
        if ($user->hasRole('administrator')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('project-manager')) {
            return $this->projectManagerDashboard();
        }

        // Default: Engineer dashboard
        return $this->engineerDashboard();
    }

    /**
     * Administrator Dashboard - Full system overview
     */
    private function adminDashboard()
    {
        $stats = [
            'active_projects' => Project::where('status', 'in_progress')->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'total_clients' => Client::count(),
            'monthly_revenue' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['active', 'signed'])
                ->sum('value'),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'pending_reviews' => Task::where('status', 'review')->count(),
            'completed_this_month' => Task::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
        ];

        $usersByRole = Role::withCount('users')->get();

        $recentTasks = Task::with(['project', 'assignedTo'])
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->take(10)
            ->get();

        $projectProgress = Project::where('status', 'in_progress')
            ->select('id', 'name', 'progress')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        $recentActivities = $this->getRecentActivities();

        return view('admin.dashboard', [
            'dashboardType' => 'admin',
            'stats' => $stats,
            'usersByRole' => $usersByRole,
            'recentTasks' => $recentTasks,
            'projectProgress' => $projectProgress,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Project Manager Dashboard - Project & team focused
     */
    private function projectManagerDashboard()
    {
        $user = auth()->user();

        $myProjects = $user->managedProjects()
            ->with(['client', 'tasks'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();

        $projectIds = $myProjects->pluck('id');

        $stats = [
            'my_projects_count' => $myProjects->count(),
            'team_pending_tasks' => Task::whereIn('project_id', $projectIds)
                ->where('status', 'pending')->count(),
            'team_in_progress' => Task::whereIn('project_id', $projectIds)
                ->where('status', 'in_progress')->count(),
            'pending_reviews' => Task::whereIn('project_id', $projectIds)
                ->where('status', 'review')->count(),
            'overdue_tasks' => Task::whereIn('project_id', $projectIds)
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
        ];

        $teamTasks = Task::whereIn('project_id', $projectIds)
            ->with(['project', 'assignedTo'])
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->take(15)
            ->get();

        $upcomingMilestones = Milestone::whereIn('project_id', $projectIds)
            ->with('project')
            ->whereNotIn('status', ['completed'])
            ->orderBy('target_date')
            ->take(5)
            ->get();

        // Team members working on their projects
        $teamMembers = User::whereHas('assignedTasks', function ($q) use ($projectIds) {
            $q->whereIn('project_id', $projectIds)
                ->whereIn('status', ['pending', 'in_progress', 'review']);
        })->withCount(['assignedTasks' => function ($q) use ($projectIds) {
            $q->whereIn('project_id', $projectIds)
                ->whereIn('status', ['pending', 'in_progress', 'review']);
        }])->get();

        return view('admin.dashboard', [
            'dashboardType' => 'project-manager',
            'stats' => $stats,
            'myProjects' => $myProjects,
            'teamTasks' => $teamTasks,
            'upcomingMilestones' => $upcomingMilestones,
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * Engineer Dashboard - Task focused
     */
    private function engineerDashboard()
    {
        $user = auth()->user();

        // My assigned tasks, priority sorted
        $myTasks = Task::with(['project', 'projectService.service', 'milestone'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->get();

        // Tasks pending my review (I'm the reviewer)
        $pendingMyReview = Task::with(['project', 'assignedTo'])
            ->where('reviewed_by', $user->id)
            ->where('status', 'review')
            ->whereNull('reviewed_at')
            ->orderBy('due_date')
            ->get();

        // Tasks I submitted for review (awaiting approval)
        $mySubmittedForReview = Task::with(['project', 'reviewedBy'])
            ->where('assigned_to', $user->id)
            ->where('status', 'review')
            ->get();

        // Workload/capacity
        $currentWorkload = $user->getCurrentWorkload();
        $weeklyCapacity = $user->getAvailableHoursForWeek();
        $totalCapacity = $currentWorkload + $weeklyCapacity;
        $utilizationPercentage = $totalCapacity > 0
            ? round(($currentWorkload / $totalCapacity) * 100)
            : 0;

        $stats = [
            'total_assigned' => $myTasks->count(),
            'urgent_tasks' => $myTasks->where('priority', 'urgent')->count(),
            'high_priority' => $myTasks->whereIn('priority', ['urgent', 'high'])->count(),
            'overdue' => $myTasks->filter(fn($t) => $t->isOverdue())->count(),
            'pending_review' => $pendingMyReview->count(),
            'awaiting_approval' => $mySubmittedForReview->count(),
            'current_workload_hours' => $currentWorkload,
            'available_hours' => $weeklyCapacity,
            'utilization' => $utilizationPercentage,
        ];

        // Calendar data - tasks for the next 14 days
        $calendarTasks = $myTasks->filter(function ($task) {
            return $task->due_date && $task->due_date->isBetween(now(), now()->addDays(14));
        })->groupBy(function ($task) {
            return $task->due_date->format('Y-m-d');
        });

        // Recently completed (for motivation)
        $recentlyCompleted = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'dashboardType' => 'engineer',
            'stats' => $stats,
            'myTasks' => $myTasks,
            'pendingMyReview' => $pendingMyReview,
            'mySubmittedForReview' => $mySubmittedForReview,
            'calendarTasks' => $calendarTasks,
            'recentlyCompleted' => $recentlyCompleted,
        ]);
    }

    /**
     * Get team workload data for API
     */
    public function getTeamWorkload()
    {
        $users = User::where('is_active', true)
            ->withCount(['assignedTasks' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])])
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'allocated' => $u->getCurrentWorkload(),
                'capacity' => 40,
                'utilization' => min(100, round(($u->getCurrentWorkload() / 40) * 100)),
            ]);

        return response()->json($users);
    }

    /**
     * Approvals page - Task/milestone approval workflow
     */
    public function approvals()
    {
        $user = auth()->user();

        // Tasks pending approval (status = review)
        $pendingTaskApprovals = Task::with(['project', 'assignedTo', 'reviewedBy'])
            ->where('status', 'review')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->get();

        // Milestones pending approval
        $pendingMilestoneApprovals = Milestone::with(['project'])
            ->where('status', 'pending_approval')
            ->orderBy('target_date')
            ->get();

        // Recently approved tasks
        $recentlyApproved = Task::with(['project', 'assignedTo', 'reviewedBy'])
            ->where('status', 'completed')
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->take(10)
            ->get();

        // Recently rejected
        $recentlyRejected = Task::with(['project', 'assignedTo', 'reviewedBy'])
            ->where('status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'pending_tasks' => $pendingTaskApprovals->count(),
            'pending_milestones' => $pendingMilestoneApprovals->count(),
            'approved_today' => Task::where('status', 'completed')
                ->whereDate('reviewed_at', today())
                ->count(),
            'rejected_today' => Task::where('status', 'rejected')
                ->whereDate('updated_at', today())
                ->count(),
        ];

        return view('admin.approvals', compact(
            'pendingTaskApprovals',
            'pendingMilestoneApprovals',
            'recentlyApproved',
            'recentlyRejected',
            'stats'
        ));
    }

    /**
     * Analytics page - Project/task analytics with charts
     */
    public function analytics()
    {
        // Project status distribution
        $projectsByStatus = Project::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Tasks by status
        $tasksByStatus = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Monthly task completion trend (last 6 months)
        $monthlyTaskCompletion = Task::where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(completed_at) as year'),
                DB::raw('MONTH(completed_at) as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Projects by client
        $projectsByClient = Project::with('client')
            ->select('client_id', DB::raw('count(*) as count'))
            ->groupBy('client_id')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Task completion rate by user
        $userPerformance = User::where('is_active', true)
            ->withCount([
                'assignedTasks as total_tasks',
                'assignedTasks as completed_tasks' => fn($q) => $q->where('status', 'completed'),
                'assignedTasks as overdue_tasks' => fn($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['completed', 'cancelled']),
            ])
            ->having('total_tasks', '>', 0)
            ->orderBy('completed_tasks', 'desc')
            ->take(10)
            ->get()
            ->map(fn($u) => [
                'name' => $u->name,
                'total' => $u->total_tasks,
                'completed' => $u->completed_tasks,
                'overdue' => $u->overdue_tasks,
                'completion_rate' => round(($u->completed_tasks / $u->total_tasks) * 100),
            ]);

        // Average task duration by priority
        $avgDurationByPriority = Task::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->select('priority', DB::raw('AVG(DATEDIFF(completed_at, created_at)) as avg_days'))
            ->groupBy('priority')
            ->get();

        // Contract value by month
        $monthlyContractValue = Contract::whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(value) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $stats = [
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_clients' => Client::count(),
            'total_contract_value' => Contract::whereIn('status', ['active', 'signed'])->sum('value'),
            'avg_project_duration' => Project::where('status', 'completed')
                ->whereNotNull('actual_start_date')
                ->whereNotNull('actual_end_date')
                ->selectRaw('AVG(DATEDIFF(actual_end_date, actual_start_date)) as avg')
                ->value('avg') ?? 0,
        ];

        return view('admin.analytics', compact(
            'projectsByStatus',
            'tasksByStatus',
            'monthlyTaskCompletion',
            'projectsByClient',
            'userPerformance',
            'avgDurationByPriority',
            'monthlyContractValue',
            'stats'
        ));
    }

    /**
     * Reports page - Report generation interface
     */
    public function reports()
    {
        // Available report types
        $reportTypes = [
            [
                'id' => 'project-summary',
                'name' => 'Project Summary Report',
                'description' => 'Overview of all projects with status, progress, and key metrics',
                'icon' => 'fa-project-diagram',
            ],
            [
                'id' => 'task-status',
                'name' => 'Task Status Report',
                'description' => 'Detailed breakdown of tasks by status, priority, and assignee',
                'icon' => 'fa-tasks',
            ],
            [
                'id' => 'team-performance',
                'name' => 'Team Performance Report',
                'description' => 'Individual and team productivity metrics',
                'icon' => 'fa-users',
            ],
            [
                'id' => 'financial',
                'name' => 'Financial Report',
                'description' => 'Contract values, invoicing, and revenue analysis',
                'icon' => 'fa-chart-line',
            ],
            [
                'id' => 'client-activity',
                'name' => 'Client Activity Report',
                'description' => 'Client engagement and project history',
                'icon' => 'fa-user-tie',
            ],
            [
                'id' => 'milestone-tracking',
                'name' => 'Milestone Tracking Report',
                'description' => 'Milestone completion rates and timeline analysis',
                'icon' => 'fa-flag-checkered',
            ],
        ];

        // Quick stats for the reports page
        $stats = [
            'projects_this_month' => Project::whereMonth('created_at', now()->month)->count(),
            'tasks_completed_this_month' => Task::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)->count(),
            'revenue_this_month' => Contract::whereMonth('created_at', now()->month)
                ->whereIn('status', ['active', 'signed'])->sum('value'),
        ];

        // Recent generated reports (placeholder for future implementation)
        $recentReports = [];

        return view('admin.reports', compact('reportTypes', 'stats', 'recentReports'));
    }

    /**
     * Generate report based on type and format
     */
    public function generateReport(Request $request)
    {
        $type = $request->input('type');
        $format = $request->input('format', 'pdf'); // 'pdf' or 'excel'
        $filters = $request->input('filters', []);

        $reportService = app(\App\Services\ReportService::class);

        return match($type) {
            'project-summary' => $format === 'pdf'
                ? $reportService->generateProjectSummaryPDF($filters)
                : $reportService->generateProjectSummaryExcel($filters),
            'task-status' => $format === 'pdf'
                ? $reportService->generateTaskStatusPDF($filters)
                : $reportService->generateTaskStatusExcel($filters),
            'team-performance' => $format === 'pdf'
                ? $reportService->generateTeamPerformancePDF($filters)
                : $reportService->generateTeamPerformanceExcel($filters),
            'financial' => $format === 'pdf'
                ? $reportService->generateFinancialPDF($filters)
                : $reportService->generateFinancialExcel($filters),
            'client-activity' => $format === 'pdf'
                ? $reportService->generateClientActivityPDF($filters)
                : $reportService->generateClientActivityExcel($filters),
            'milestone-tracking' => $format === 'pdf'
                ? $reportService->generateMilestoneTrackingPDF($filters)
                : $reportService->generateMilestoneTrackingExcel($filters),
            default => abort(404, 'Report type not found'),
        };
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent contracts
        $recentContracts = Contract::with('client')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get()
            ->map(function ($contract) {
                return [
                    'type' => 'contract',
                    'icon' => 'fa-file-contract',
                    'color' => 'rgba(243, 200, 135, 0.2)',
                    'icon_color' => 'var(--secondary-color)',
                    'title' => 'New Contract ' . ($contract->status === 'signed' ? 'Signed' : 'Created'),
                    'description' => $contract->title . ' - ' . ($contract->client->name ?? 'Unknown Client'),
                    'time' => $contract->created_at->diffForHumans(),
                ];
            });

        // Recent tasks completed
        $completedTasks = Task::with('project')
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(2)
            ->get()
            ->map(function ($task) {
                return [
                    'type' => 'task',
                    'icon' => 'fa-check-circle',
                    'color' => 'rgba(76, 175, 80, 0.2)',
                    'icon_color' => '#4caf50',
                    'title' => 'Task Completed',
                    'description' => $task->title,
                    'time' => ($task->completed_at ?? $task->updated_at)->diffForHumans(),
                ];
            });

        // Recent projects
        $recentProjects = Project::orderBy('created_at', 'desc')
            ->take(1)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'icon' => 'fa-project-diagram',
                    'color' => 'rgba(33, 150, 243, 0.2)',
                    'icon_color' => '#2196f3',
                    'title' => 'New Project Created',
                    'description' => $project->name,
                    'time' => $project->created_at->diffForHumans(),
                ];
            });

        return $activities
            ->merge($recentContracts)
            ->merge($completedTasks)
            ->merge($recentProjects)
            ->take(5)
            ->values();
    }
}
