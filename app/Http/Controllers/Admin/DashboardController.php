<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Main statistics
        $stats = [
            'active_projects' => Project::where('status', 'in_progress')->count(),
            'pending_tasks' => Task::where('status', 'pending')->count(),
            'total_clients' => Client::count(),
            'new_clients_this_week' => Client::where('created_at', '>=', now()->subWeek())->count(),
            'monthly_revenue' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['active', 'signed'])
                ->sum('value'),
            'pending_approvals' => Task::where('status', 'review')->count(),
        ];

        // Recent tasks for the dashboard
        $recentTasks = Task::with(['project', 'assignedTo'])
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->take(5)
            ->get();

        // Projects with progress
        $projectProgress = Project::where('status', 'in_progress')
            ->select('id', 'name', 'progress')
            ->orderBy('updated_at', 'desc')
            ->take(4)
            ->get();

        // Recent activities (simulated from recent model changes)
        $recentActivities = $this->getRecentActivities();

        return view('admin.dashboard', compact('stats', 'recentTasks', 'projectProgress', 'recentActivities'));
    }

    public function projects()
    {
        $stats = [
            'total' => Project::count(),
            'planning' => Project::where('status', 'planning')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
        ];

        // Projects by main service
        $projectsByService = Project::select('main_service_id', DB::raw('count(*) as count'))
            ->with('mainService')
            ->groupBy('main_service_id')
            ->get();

        // Recent projects
        $recentProjects = Project::with(['client', 'projectManager'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Upcoming deadlines
        $upcomingDeadlines = Project::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addMonth())
            ->where('status', '!=', 'completed')
            ->orderBy('end_date')
            ->take(5)
            ->get();

        return view('admin.dashboards.projects', compact('stats', 'projectsByService', 'recentProjects', 'upcomingDeadlines'));
    }

    public function services()
    {
        // Service statistics
        $projectServices = DB::table('project_services')
            ->join('services', 'project_services.service_id', '=', 'services.id')
            ->join('service_stages', 'services.service_stage_id', '=', 'service_stages.id')
            ->select('service_stages.name as stage_name', DB::raw('count(*) as count'))
            ->groupBy('service_stages.id', 'service_stages.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Completion rates by service stage
        $completionRates = DB::table('project_services')
            ->join('service_stages', 'project_services.service_stage_id', '=', 'service_stages.id')
            ->select(
                'service_stages.name',
                DB::raw('count(*) as total'),
                DB::raw('sum(case when is_completed = 1 then 1 else 0 end) as completed')
            )
            ->groupBy('service_stages.id', 'service_stages.name')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->completed / $item->total) * 100) : 0;
                return $item;
            });

        return view('admin.dashboards.services', compact('projectServices', 'completionRates'));
    }

    public function pipeline()
    {
        // Pipeline stages
        $pipeline = [
            'prospect' => Client::where('status', 'prospect')->count(),
            'planning' => Project::where('status', 'planning')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed' => Project::where('status', 'completed')->count(),
        ];

        // Monthly project count
        $monthlyProjects = Project::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // Contract values by month
        $monthlyRevenue = Contract::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('sum(value) as total')
        )
            ->whereYear('created_at', now()->year)
            ->whereIn('status', ['active', 'signed'])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        return view('admin.dashboards.pipeline', compact('pipeline', 'monthlyProjects', 'monthlyRevenue'));
    }

    public function finance()
    {
        // Financial overview
        $stats = [
            'total_contract_value' => Contract::whereIn('status', ['active', 'signed'])->sum('value'),
            'this_month' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['active', 'signed'])
                ->sum('value'),
            'total_contracts' => Contract::count(),
            'active_contracts' => Contract::whereIn('status', ['active', 'signed'])->count(),
        ];

        // Contracts by status
        $contractsByStatus = Contract::select('status', DB::raw('count(*) as count'), DB::raw('sum(value) as total'))
            ->groupBy('status')
            ->get();

        // Recent contracts
        $recentContracts = Contract::with(['client', 'project'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboards.finance', compact('stats', 'contractsByStatus', 'recentContracts'));
    }

    public function hr()
    {
        // Team statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'project_managers' => User::whereHas('roles', function ($q) {
                $q->where('slug', 'project-manager');
            })->count(),
        ];

        // Users with task counts
        $userTaskCounts = User::withCount([
            'assignedTasks',
            'assignedTasks as pending_tasks_count' => function ($q) {
                $q->where('status', 'pending');
            },
            'assignedTasks as in_progress_tasks_count' => function ($q) {
                $q->where('status', 'in_progress');
            }
        ])
            ->where('is_active', true)
            ->orderBy('assigned_tasks_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboards.hr', compact('stats', 'userTaskCounts'));
    }

    public function performance()
    {
        // Task completion stats
        $taskStats = [
            'total' => Task::count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
        ];
        $taskStats['completion_rate'] = $taskStats['total'] > 0
            ? round(($taskStats['completed'] / $taskStats['total']) * 100)
            : 0;

        // Milestone completion
        $milestoneStats = DB::table('milestones')
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "completed" then 1 else 0 end) as completed')
            )
            ->first();

        // Average project duration
        $avgDuration = Project::where('status', 'completed')
            ->whereNotNull('actual_start_date')
            ->whereNotNull('actual_end_date')
            ->select(DB::raw('AVG(DATEDIFF(actual_end_date, actual_start_date)) as avg_days'))
            ->first();

        return view('admin.dashboards.performance', compact('taskStats', 'milestoneStats', 'avgDuration'));
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
                    'time' => $contract->created_at,
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
                    'time' => $task->completed_at ?? $task->updated_at,
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
                    'time' => $project->created_at,
                ];
            });

        return $activities
            ->merge($recentContracts)
            ->merge($completedTasks)
            ->merge($recentProjects)
            ->sortByDesc('time')
            ->take(5)
            ->values();
    }
}
