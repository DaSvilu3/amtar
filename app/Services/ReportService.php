<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Milestone;
use App\Exports\ProjectSummaryExport;
use App\Exports\TaskStatusExport;
use App\Exports\TeamPerformanceExport;
use App\Exports\FinancialExport;
use App\Exports\ClientActivityExport;
use App\Exports\MilestoneTrackingExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate Project Summary PDF Report
     */
    public function generateProjectSummaryPDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $projects = $this->getProjectsData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.project-summary', [
            'projects' => $projects,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('project-summary-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Project Summary Excel Report
     */
    public function generateProjectSummaryExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ProjectSummaryExport($filters), 'project-summary-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate Task Status PDF Report
     */
    public function generateTaskStatusPDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $tasks = $this->getTasksData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.task-status', [
            'tasks' => $tasks,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('task-status-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Task Status Excel Report
     */
    public function generateTaskStatusExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new TaskStatusExport($filters), 'task-status-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate Team Performance PDF Report
     */
    public function generateTeamPerformancePDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $teamData = $this->getTeamPerformanceData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.team-performance', [
            'team_data' => $teamData,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('team-performance-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Team Performance Excel Report
     */
    public function generateTeamPerformanceExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new TeamPerformanceExport($filters), 'team-performance-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate Financial PDF Report
     */
    public function generateFinancialPDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $financialData = $this->getFinancialData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.financial', [
            'financial_data' => $financialData,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('financial-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Financial Excel Report
     */
    public function generateFinancialExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new FinancialExport($filters), 'financial-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate Client Activity PDF Report
     */
    public function generateClientActivityPDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $clientData = $this->getClientActivityData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.client-activity', [
            'client_data' => $clientData,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('client-activity-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Client Activity Excel Report
     */
    public function generateClientActivityExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ClientActivityExport($filters), 'client-activity-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Generate Milestone Tracking PDF Report
     */
    public function generateMilestoneTrackingPDF(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $milestones = $this->getMilestoneData($filters);

        $pdf = Pdf::loadView('admin.reports.pdf.milestone-tracking', [
            'milestones' => $milestones,
            'filters' => $filters,
            'generated_at' => now(),
        ]);

        return $pdf->download('milestone-tracking-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate Milestone Tracking Excel Report
     */
    public function generateMilestoneTrackingExcel(array $filters = []): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new MilestoneTrackingExport($filters), 'milestone-tracking-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Get Projects Data with Filters
     */
    private function getProjectsData(array $filters = []): Collection
    {
        $query = Project::with(['client', 'projectManager', 'mainService', 'subService']);

        $query = $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get Tasks Data with Filters
     */
    private function getTasksData(array $filters = []): Collection
    {
        $query = Task::with(['project', 'assignedTo', 'reviewedBy', 'projectService']);

        $query = $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get Team Performance Data
     */
    private function getTeamPerformanceData(array $filters = []): Collection
    {
        $query = User::with(['assignedTasks', 'capacities'])
            ->whereHas('roles', function ($q) {
                $q->whereIn('slug', ['engineer', 'project-manager']);
            });

        if (!empty($filters['date_from'])) {
            $query->whereHas('assignedTasks', function ($q) use ($filters) {
                $q->where('created_at', '>=', $filters['date_from']);
            });
        }

        return $query->get()->map(function ($user) {
            return [
                'user' => $user,
                'total_tasks' => $user->assignedTasks->count(),
                'completed_tasks' => $user->assignedTasks->where('status', 'completed')->count(),
                'pending_tasks' => $user->assignedTasks->where('status', 'pending')->count(),
                'in_progress_tasks' => $user->assignedTasks->where('status', 'in_progress')->count(),
                'completion_rate' => $user->assignedTasks->count() > 0
                    ? round(($user->assignedTasks->where('status', 'completed')->count() / $user->assignedTasks->count()) * 100, 2)
                    : 0,
                'average_hours' => $user->assignedTasks->avg('actual_hours') ?? 0,
            ];
        });
    }

    /**
     * Get Financial Data
     */
    private function getFinancialData(array $filters = []): array
    {
        $projectsQuery = Project::query();
        $this->applyFilters($projectsQuery, $filters);

        $projects = $projectsQuery->get();

        return [
            'total_budget' => $projects->sum('budget'),
            'projects_by_status' => $projects->groupBy('status')->map(fn($group) => [
                'count' => $group->count(),
                'total_budget' => $group->sum('budget'),
            ]),
            'projects' => $projects,
            'average_budget' => $projects->avg('budget'),
        ];
    }

    /**
     * Get Client Activity Data
     */
    private function getClientActivityData(array $filters = []): Collection
    {
        $query = Client::with(['projects', 'contracts']);

        if (!empty($filters['date_from'])) {
            $query->whereHas('projects', function ($q) use ($filters) {
                $q->where('created_at', '>=', $filters['date_from']);
            });
        }

        return $query->get()->map(function ($client) {
            return [
                'client' => $client,
                'total_projects' => $client->projects->count(),
                'active_projects' => $client->projects->whereIn('status', ['active', 'in_progress'])->count(),
                'completed_projects' => $client->projects->where('status', 'completed')->count(),
                'total_contracts_value' => $client->contracts->sum('contract_value'),
            ];
        });
    }

    /**
     * Get Milestone Data
     */
    private function getMilestoneData(array $filters = []): Collection
    {
        $query = Milestone::with(['project']);

        $query = $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Apply Common Filters to Query
     */
    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query;
    }
}
