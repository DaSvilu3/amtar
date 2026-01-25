<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['assignedTasks', 'capacities'])
            ->whereHas('roles', function ($q) {
                $q->whereIn('slug', ['engineer', 'project-manager']);
            });

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Total Tasks',
            'Completed Tasks',
            'In Progress',
            'Pending',
            'Completion Rate (%)',
            'Avg Hours per Task',
            'Current Utilization (%)',
        ];
    }

    public function map($user): array
    {
        $completedTasks = $user->assignedTasks->where('status', 'completed')->count();
        $totalTasks = $user->assignedTasks->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        $latestCapacity = $user->capacities->sortByDesc('week_start_date')->first();

        return [
            $user->name,
            $user->email,
            $totalTasks,
            $completedTasks,
            $user->assignedTasks->where('status', 'in_progress')->count(),
            $user->assignedTasks->where('status', 'pending')->count(),
            $completionRate,
            round($user->assignedTasks->avg('actual_hours') ?? 0, 2),
            $latestCapacity?->utilization_percentage ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
