<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Project::with(['client', 'projectManager', 'mainService']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Project Number',
            'Project Name',
            'Client',
            'Project Manager',
            'Service',
            'Status',
            'Budget',
            'Progress (%)',
            'Start Date',
            'End Date',
            'Location',
        ];
    }

    public function map($project): array
    {
        return [
            $project->project_number,
            $project->name,
            $project->client->name ?? 'N/A',
            $project->projectManager->name ?? 'N/A',
            $project->mainService->name ?? 'N/A',
            ucfirst($project->status),
            number_format($project->budget, 2),
            $project->progress,
            $project->start_date?->format('Y-m-d'),
            $project->end_date?->format('Y-m-d'),
            $project->location,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
