<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientActivityExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Client::with(['projects', 'contracts']);

        if (!empty($this->filters['date_from'])) {
            $query->whereHas('projects', function ($q) {
                $q->where('created_at', '>=', $this->filters['date_from']);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Client Name',
            'Type',
            'Email',
            'Phone',
            'Total Projects',
            'Active Projects',
            'Completed Projects',
            'Total Contract Value',
        ];
    }

    public function map($client): array
    {
        return [
            $client->name,
            ucfirst($client->type),
            $client->email,
            $client->phone,
            $client->projects->count(),
            $client->projects->whereIn('status', ['active', 'in_progress'])->count(),
            $client->projects->where('status', 'completed')->count(),
            number_format($client->contracts->sum('contract_value'), 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
