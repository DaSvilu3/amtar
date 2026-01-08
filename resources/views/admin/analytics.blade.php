@extends('layouts.admin')

@section('title', 'Analytics')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Analytics</h1>
        <p class="text-muted mb-0">Project and task performance insights</p>
    </div>
    <div>
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
</div>
@endsection

@section('content')
<style>
    .analytics-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        text-align: center;
    }

    .stat-card h3 {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary-color, #2f0e13);
        margin: 0;
    }

    .stat-card p {
        color: #64748b;
        font-size: 13px;
        margin: 8px 0 0;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .chart-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chart-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .chart-body {
        padding: 24px;
    }

    .chart-container {
        position: relative;
        height: 280px;
    }

    .chart-card.full-width {
        grid-column: span 2;
    }

    /* Status Distribution */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 16px;
    }

    .status-item {
        text-align: center;
        padding: 16px;
        border-radius: 10px;
        background: #f8fafc;
    }

    .status-item .count {
        font-size: 28px;
        font-weight: 700;
    }

    .status-item .label {
        font-size: 12px;
        color: #64748b;
        text-transform: capitalize;
        margin-top: 4px;
    }

    .status-item.planning .count { color: #9c27b0; }
    .status-item.in_progress .count { color: #2196f3; }
    .status-item.on_hold .count { color: #ff9800; }
    .status-item.completed .count { color: #4caf50; }
    .status-item.cancelled .count { color: #9e9e9e; }
    .status-item.pending .count { color: #ff9800; }
    .status-item.review .count { color: #9c27b0; }
    .status-item.rejected .count { color: #f44336; }

    /* Performance Table */
    .performance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .performance-table th {
        text-align: left;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .performance-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #1e293b;
    }

    .performance-table tr:last-child td {
        border-bottom: none;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color, #2f0e13), #5a2a30);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
    }

    .completion-bar {
        width: 100%;
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .completion-bar .fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s;
    }

    .completion-bar .fill.high { background: #4caf50; }
    .completion-bar .fill.medium { background: #ff9800; }
    .completion-bar .fill.low { background: #f44336; }

    /* Client Bar Chart */
    .bar-chart {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .bar-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .bar-label {
        width: 140px;
        font-size: 13px;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bar-track {
        flex: 1;
        height: 24px;
        background: #f1f5f9;
        border-radius: 6px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color, #2f0e13), #5a2a30);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        color: white;
        font-size: 12px;
        font-weight: 600;
        min-width: 30px;
    }

    /* Priority Duration */
    .priority-duration {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .duration-item {
        text-align: center;
        padding: 20px;
        border-radius: 10px;
        background: #f8fafc;
    }

    .duration-item .priority {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .duration-item .days {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }

    .duration-item .days span {
        font-size: 14px;
        font-weight: 400;
        color: #64748b;
    }

    .duration-item.urgent .priority { color: #f44336; }
    .duration-item.high .priority { color: #ff9800; }
    .duration-item.medium .priority { color: #2196f3; }
    .duration-item.low .priority { color: #9e9e9e; }

    @media (max-width: 1200px) {
        .analytics-stats {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .chart-grid {
            grid-template-columns: 1fr;
        }

        .chart-card.full-width {
            grid-column: span 1;
        }
    }

    @media (max-width: 768px) {
        .analytics-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .priority-duration {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media print {
        .chart-card {
            break-inside: avoid;
        }
    }
</style>

<!-- Overview Stats -->
<div class="analytics-stats">
    <div class="stat-card">
        <h3>{{ $stats['total_projects'] }}</h3>
        <p>Total Projects</p>
    </div>
    <div class="stat-card">
        <h3>{{ $stats['total_tasks'] }}</h3>
        <p>Total Tasks</p>
    </div>
    <div class="stat-card">
        <h3>{{ $stats['total_clients'] }}</h3>
        <p>Total Clients</p>
    </div>
    <div class="stat-card">
        <h3>{{ number_format($stats['total_contract_value']) }}</h3>
        <p>Contract Value (OMR)</p>
    </div>
    <div class="stat-card">
        <h3>{{ round($stats['avg_project_duration']) }}</h3>
        <p>Avg. Days per Project</p>
    </div>
</div>

<div class="chart-grid">
    <!-- Project Status Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-project-diagram text-primary me-2"></i>Project Status</h4>
        </div>
        <div class="chart-body">
            <div class="status-grid">
                @foreach($projectsByStatus as $status)
                    <div class="status-item {{ $status->status }}">
                        <div class="count">{{ $status->count }}</div>
                        <div class="label">{{ str_replace('_', ' ', $status->status) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Task Status Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-tasks text-info me-2"></i>Task Status</h4>
        </div>
        <div class="chart-body">
            <div class="status-grid">
                @foreach($tasksByStatus as $status)
                    <div class="status-item {{ $status->status }}">
                        <div class="count">{{ $status->count }}</div>
                        <div class="label">{{ str_replace('_', ' ', $status->status) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Projects by Client -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-user-tie text-success me-2"></i>Projects by Client (Top 10)</h4>
        </div>
        <div class="chart-body">
            <div class="bar-chart">
                @php $maxProjects = $projectsByClient->max('count') ?: 1; @endphp
                @forelse($projectsByClient as $item)
                    <div class="bar-item">
                        <span class="bar-label">{{ $item->client->name ?? 'Unknown' }}</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ ($item->count / $maxProjects) * 100 }}%">
                                {{ $item->count }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No client data available.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Avg Task Duration by Priority -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-clock text-warning me-2"></i>Avg. Task Duration by Priority</h4>
        </div>
        <div class="chart-body">
            <div class="priority-duration">
                @foreach(['urgent', 'high', 'medium', 'low'] as $priority)
                    @php
                        $item = $avgDurationByPriority->firstWhere('priority', $priority);
                        $avgDays = $item ? round($item->avg_days, 1) : 0;
                    @endphp
                    <div class="duration-item {{ $priority }}">
                        <div class="priority">{{ ucfirst($priority) }}</div>
                        <div class="days">{{ $avgDays }} <span>days</span></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Task Completion -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-chart-line text-success me-2"></i>Monthly Task Completion (6 Months)</h4>
        </div>
        <div class="chart-body">
            <div class="chart-container">
                <canvas id="taskCompletionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Contract Value -->
    <div class="chart-card">
        <div class="chart-header">
            <h4><i class="fas fa-chart-bar text-info me-2"></i>Monthly Contract Value ({{ date('Y') }})</h4>
        </div>
        <div class="chart-body">
            <div class="chart-container">
                <canvas id="contractValueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Team Performance -->
    <div class="chart-card full-width">
        <div class="chart-header">
            <h4><i class="fas fa-users text-primary me-2"></i>Team Performance (Top 10)</h4>
        </div>
        <div class="chart-body" style="padding: 0;">
            <table class="performance-table">
                <thead>
                    <tr>
                        <th>Team Member</th>
                        <th>Total Tasks</th>
                        <th>Completed</th>
                        <th>Overdue</th>
                        <th>Completion Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userPerformance as $user)
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ strtoupper(substr($user['name'], 0, 2)) }}</div>
                                    <span>{{ $user['name'] }}</span>
                                </div>
                            </td>
                            <td>{{ $user['total'] }}</td>
                            <td style="color: #4caf50; font-weight: 600;">{{ $user['completed'] }}</td>
                            <td style="color: {{ $user['overdue'] > 0 ? '#f44336' : '#9e9e9e' }};">{{ $user['overdue'] }}</td>
                            <td style="width: 200px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="completion-bar">
                                        @php
                                            $rate = $user['completion_rate'];
                                            $colorClass = $rate >= 80 ? 'high' : ($rate >= 50 ? 'medium' : 'low');
                                        @endphp
                                        <div class="fill {{ $colorClass }}" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span style="font-weight: 600; min-width: 40px;">{{ $rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 40px;">
                                No performance data available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Task Completion Chart
    const taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
    const taskCompletionData = @json($monthlyTaskCompletion);

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const last6Months = [];
    const completionCounts = [];

    for (let i = 5; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        const monthLabel = months[date.getMonth()] + ' ' + date.getFullYear().toString().substr(2);
        last6Months.push(monthLabel);

        const dataPoint = taskCompletionData.find(d => d.month === (date.getMonth() + 1) && d.year === date.getFullYear());
        completionCounts.push(dataPoint ? dataPoint.count : 0);
    }

    new Chart(taskCompletionCtx, {
        type: 'line',
        data: {
            labels: last6Months,
            datasets: [{
                label: 'Tasks Completed',
                data: completionCounts,
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4caf50',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Monthly Contract Value Chart
    const contractValueCtx = document.getElementById('contractValueChart').getContext('2d');
    const contractValueData = @json($monthlyContractValue);

    const allMonths = months;
    const contractValues = allMonths.map((_, index) => {
        const dataPoint = contractValueData.find(d => d.month === (index + 1));
        return dataPoint ? dataPoint.total : 0;
    });

    new Chart(contractValueCtx, {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'Contract Value (OMR)',
                data: contractValues,
                backgroundColor: 'rgba(47, 14, 19, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' OMR';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
