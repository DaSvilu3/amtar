<?php

namespace App\Exports;

use App\Models\Milestone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MilestoneTrackingExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Milestone::with(['project']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        return $query->orderBy('start_date')->get();
    }

    public function headings(): array
    {
        return [
            'Milestone Name',
            'Project',
            'Status',
            'Progress (%)',
            'Start Date',
            'End Date',
            'Payment %',
            'Invoiced',
        ];
    }

    public function map($milestone): array
    {
        return [
            $milestone->name,
            $milestone->project->name ?? 'N/A',
            ucfirst($milestone->status),
            $milestone->progress,
            $milestone->start_date?->format('Y-m-d'),
            $milestone->end_date?->format('Y-m-d'),
            $milestone->payment_percentage,
            $milestone->is_invoiced ? 'Yes' : 'No',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
