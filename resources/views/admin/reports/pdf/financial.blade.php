<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Financial Report</title>
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
        .summary-box {
            background-color: #f8fafc;
            border: 2px solid #2f0e13;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-box .item {
            display: inline-block;
            width: 48%;
            margin-bottom: 10px;
        }
        .summary-box .label {
            font-weight: bold;
            color: #2f0e13;
        }
        .summary-box .value {
            font-size: 14pt;
            color: #10b981;
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
        .amount {
            font-weight: bold;
            color: #10b981;
        }
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
        <h2>التقرير المالي</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    <div class="summary-box">
        <div class="item">
            <div class="label">إجمالي الميزانية:</div>
            <div class="value">{{ number_format($financial_data['total_budget'], 2) }} OMR</div>
        </div>
        <div class="item">
            <div class="label">متوسط الميزانية:</div>
            <div class="value">{{ number_format($financial_data['average_budget'], 2) }} OMR</div>
        </div>
        <div class="item">
            <div class="label">عدد المشاريع:</div>
            <div class="value">{{ $financial_data['projects']->count() }}</div>
        </div>
    </div>

    <h3>المشاريع حسب الحالة</h3>
    <table style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th>الحالة</th>
                <th>عدد المشاريع</th>
                <th>إجمالي الميزانية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financial_data['projects_by_status'] as $status => $data)
            <tr>
                <td>{{ ucfirst($status) }}</td>
                <td>{{ $data['count'] }}</td>
                <td class="amount">{{ number_format($data['total_budget'], 2) }} OMR</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>تفاصيل المشاريع</h3>
    <table>
        <thead>
            <tr>
                <th>رقم المشروع</th>
                <th>اسم المشروع</th>
                <th>العميل</th>
                <th>الميزانية</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($financial_data['projects'] as $project)
            <tr>
                <td>{{ $project->project_number }}</td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->client->name ?? 'N/A' }}</td>
                <td class="amount">{{ number_format($project->budget, 2) }} OMR</td>
                <td>{{ ucfirst($project->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">لا توجد مشاريع</td>
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
