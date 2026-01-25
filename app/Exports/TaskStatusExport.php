<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskStatusExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Task::with(['project', 'assignedTo', 'reviewedBy']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Task Title',
            'Project',
            'Assigned To',
            'Status',
            'Priority',
            'Progress (%)',
            'Estimated Hours',
            'Actual Hours',
            'Due Date',
            'Completed At',
        ];
    }

    public function map($task): array
    {
        return [
            $task->title,
            $task->project->name ?? 'N/A',
            $task->assignedTo->name ?? 'Unassigned',
            ucfirst($task->status),
            ucfirst($task->priority),
            $task->progress,
            $task->estimated_hours,
            $task->actual_hours ?? 0,
            $task->due_date?->format('Y-m-d'),
            $task->completed_at?->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
