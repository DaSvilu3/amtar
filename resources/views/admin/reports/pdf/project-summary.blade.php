<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Project Summary Report</title>
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
        .header .subtitle {
            color: #666;
            font-size: 12pt;
        }
        .info-section {
            margin-bottom: 20px;
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
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .status-active { background-color: #10b981; color: white; }
        .status-planning { background-color: #3b82f6; color: white; }
        .status-completed { background-color: #6b7280; color: white; }
        .status-on_hold { background-color: #f59e0b; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>نظام عمطر الهندسي</h1>
        <div class="subtitle">AMTAR Engineering System</div>
        <h2>تقرير ملخص المشاريع</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    @if(!empty($filters))
    <div class="info-section">
        <strong>الفلاتر المطبقة:</strong>
        @if(!empty($filters['status']))
            <span>الحالة: {{ ucfirst($filters['status']) }}</span>
        @endif
        @if(!empty($filters['date_from']))
            <span> | من: {{ $filters['date_from'] }}</span>
        @endif
        @if(!empty($filters['date_to']))
            <span> | إلى: {{ $filters['date_to'] }}</span>
        @endif
    </div>
    @endif

    <div class="info-section">
        <strong>إجمالي المشاريع:</strong> {{ $projects->count() }} |
        <strong>إجمالي الميزانية:</strong> {{ number_format($projects->sum('budget'), 2) }} OMR
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم المشروع</th>
                <th>اسم المشروع</th>
                <th>العميل</th>
                <th>مدير المشروع</th>
                <th>الخدمة</th>
                <th>الحالة</th>
                <th>الميزانية</th>
                <th>التقدم</th>
                <th>تاريخ البدء</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr>
                <td>{{ $project->project_number }}</td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->client->name ?? 'N/A' }}</td>
                <td>{{ $project->projectManager->name ?? 'N/A' }}</td>
                <td>{{ $project->mainService->name ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge status-{{ $project->status }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </td>
                <td>{{ number_format($project->budget, 2) }}</td>
                <td>{{ $project->progress }}%</td>
                <td>{{ $project->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">لا توجد مشاريع</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>
            نظام عمطر الهندسي | AMTAR Engineering System<br>
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
    </div>
</body>
</html>
