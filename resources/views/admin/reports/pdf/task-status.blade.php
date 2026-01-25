<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Task Status Report</title>
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
            font-size: 9pt;
        }
        table th {
            background-color: #2f0e13;
            color: white;
            padding: 8px;
            text-align: right;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: right;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .priority-urgent { color: #dc2626; font-weight: bold; }
        .priority-high { color: #f59e0b; }
        .priority-normal { color: #3b82f6; }
        .priority-low { color: #6b7280; }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
        }
        .status-completed { background-color: #10b981; color: white; }
        .status-in_progress { background-color: #3b82f6; color: white; }
        .status-pending { background-color: #f59e0b; color: white; }
        .status-review { background-color: #8b5cf6; color: white; }
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
        <div class="subtitle">AMTAR Engineering System</div>
        <h2>تقرير حالة المهام</h2>
        <div style="font-size: 10pt; margin-top: 10px;">
            تاريخ الإصدار: {{ $generated_at->format('Y-m-d H:i') }}
        </div>
    </div>

    <div class="info-section">
        <strong>إجمالي المهام:</strong> {{ $tasks->count() }} |
        <strong>مكتملة:</strong> {{ $tasks->where('status', 'completed')->count() }} |
        <strong>قيد التنفيذ:</strong> {{ $tasks->where('status', 'in_progress')->count() }} |
        <strong>معلقة:</strong> {{ $tasks->where('status', 'pending')->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">عنوان المهمة</th>
                <th style="width: 15%;">المشروع</th>
                <th style="width: 12%;">المسند إليه</th>
                <th style="width: 10%;">الحالة</th>
                <th style="width: 8%;">الأولوية</th>
                <th style="width: 8%;">التقدم</th>
                <th style="width: 10%;">تاريخ الاستحقاق</th>
                <th style="width: 12%;">الساعات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->project->name ?? 'N/A' }}</td>
                <td>{{ $task->assignedTo->name ?? 'غير مسند' }}</td>
                <td>
                    <span class="status-badge status-{{ $task->status }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </td>
                <td class="priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</td>
                <td>{{ $task->progress }}%</td>
                <td>{{ $task->due_date?->format('Y-m-d') ?? 'N/A' }}</td>
                <td>{{ $task->estimated_hours }}h / {{ $task->actual_hours ?? 0 }}h</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">لا توجد مهام</td>
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
