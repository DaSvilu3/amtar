<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Client Activity Report</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path("fonts/DejaVuSans.ttf") }}');
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2f0e13;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #2f0e13;
            font-size: 20pt;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #2f0e13;
            color: white;
            padding: 10px;
            text-align: right;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .client-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .type-company { background-color: #3b82f6; color: white; }
        .type-individual { background-color: #10b981; color: white; }
        .type-government { background-color: #8b5cf6; color: white; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام عمطر الهندسي</h1>
        <div style="color: #666; font-size: 12pt;">AMTAR Engineering System</div>
        <h2>تقرير نشاط العملاء</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">اسم العميل</th>
                <th style="width: 10%;">النوع</th>
                <th style="width: 15%;">البريد الإلكتروني</th>
                <th style="width: 12%;">الهاتف</th>
                <th style="width: 10%;">إجمالي المشاريع</th>
                <th style="width: 10%;">نشطة</th>
                <th style="width: 10%;">مكتملة</th>
                <th style="width: 13%;">قيمة العقود</th>
            </tr>
        </thead>
        <tbody>
            @forelse($client_data as $data)
            @php $client = $data['client']; @endphp
            <tr>
                <td>{{ $client->name }}</td>
                <td>
                    <span class="client-type type-{{ $client->type }}">
                        {{ ucfirst($client->type) }}
                    </span>
                </td>
                <td style="font-size: 8pt;">{{ $client->email }}</td>
                <td style="font-size: 8pt;">{{ $client->phone }}</td>
                <td>{{ $data['total_projects'] }}</td>
                <td>{{ $data['active_projects'] }}</td>
                <td>{{ $data['completed_projects'] }}</td>
                <td style="font-weight: bold; color: #10b981;">
                    {{ number_format($data['total_contracts_value'], 2) }} OMR
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">لا توجد بيانات</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>نظام عمطر الهندسي | AMTAR Engineering System<br>
        Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
