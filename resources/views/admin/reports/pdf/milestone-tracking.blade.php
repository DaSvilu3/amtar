<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Milestone Tracking Report</title>
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
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .status-completed { background-color: #10b981; color: white; }
        .status-in_progress { background-color: #3b82f6; color: white; }
        .status-pending { background-color: #f59e0b; color: white; }
        .status-delayed { background-color: #dc2626; color: white; }
        .progress-bar {
            display: inline-block;
            width: 60px;
            height: 12px;
            background-color: #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            vertical-align: middle;
        }
        .progress-fill {
            height: 100%;
            background-color: #10b981;
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
        <h2>تقرير تتبع المعالم</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <strong>إجمالي المعالم:</strong> {{ $milestones->count() }} |
        <strong>مكتملة:</strong> {{ $milestones->where('status', 'completed')->count() }} |
        <strong>قيد التنفيذ:</strong> {{ $milestones->where('status', 'in_progress')->count() }} |
        <strong>متأخرة:</strong> {{ $milestones->where('status', 'delayed')->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">اسم المعلم</th>
                <th style="width: 20%;">المشروع</th>
                <th style="width: 12%;">الحالة</th>
                <th style="width: 13%;">التقدم</th>
                <th style="width: 12%;">تاريخ البدء</th>
                <th style="width: 12%;">تاريخ الانتهاء</th>
                <th style="width: 6%;">الدفع %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($milestones as $milestone)
            <tr>
                <td>{{ $milestone->name }}</td>
                <td>{{ $milestone->project->name ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge status-{{ $milestone->status }}">
                        {{ ucfirst($milestone->status) }}
                    </span>
                </td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $milestone->progress }}%;"></div>
                    </div>
                    {{ $milestone->progress }}%
                </td>
                <td>{{ $milestone->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $milestone->end_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $milestone->payment_percentage }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">لا توجد معالم</td>
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
