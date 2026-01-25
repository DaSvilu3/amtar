<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Team Performance Report</title>
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
        .performance-excellent { color: #10b981; font-weight: bold; }
        .performance-good { color: #3b82f6; }
        .performance-average { color: #f59e0b; }
        .performance-poor { color: #dc2626; }
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
        <h2>تقرير أداء الفريق</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم الموظف</th>
                <th>إجمالي المهام</th>
                <th>مكتملة</th>
                <th>قيد التنفيذ</th>
                <th>معلقة</th>
                <th>معدل الإنجاز</th>
                <th>متوسط الساعات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($team_data as $member)
            <tr>
                <td>{{ $member['user']->name }}</td>
                <td>{{ $member['total_tasks'] }}</td>
                <td>{{ $member['completed_tasks'] }}</td>
                <td>{{ $member['in_progress_tasks'] }}</td>
                <td>{{ $member['pending_tasks'] }}</td>
                <td class="
                    @if($member['completion_rate'] >= 80) performance-excellent
                    @elseif($member['completion_rate'] >= 60) performance-good
                    @elseif($member['completion_rate'] >= 40) performance-average
                    @else performance-poor
                    @endif
                ">
                    {{ $member['completion_rate'] }}%
                </td>
                <td>{{ number_format($member['average_hours'], 1) }}h</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">لا توجد بيانات</td>
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
