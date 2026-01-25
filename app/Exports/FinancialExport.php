<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Project::with(['client', 'contracts']);

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
            'Project Budget',
            'Contract Value',
            'Status',
            'Payment Received',
            'Outstanding',
        ];
    }

    public function map($project): array
    {
        $contractValue = $project->contracts->sum('contract_value');
        $paymentReceived = 0; // To be calculated based on invoices/payments
        $outstanding = $contractValue - $paymentReceived;

        return [
            $project->project_number,
            $project->name,
            $project->client->name ?? 'N/A',
            number_format($project->budget, 2),
            number_format($contractValue, 2),
            ucfirst($project->status),
            number_format($paymentReceived, 2),
            number_format($outstanding, 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
