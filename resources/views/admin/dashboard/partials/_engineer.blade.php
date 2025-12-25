{{-- Engineer Dashboard - Task-Focused View --}}

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="fas fa-tasks fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_assigned'] ?? 0 }}</h3>
                    <small class="text-muted">My Tasks</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['high_priority'] ?? 0 }}</h3>
                    <small class="text-muted">High Priority</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['overdue'] ?? 0 }}</h3>
                    <small class="text-muted">Overdue</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                    <i class="fas fa-clipboard-check fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['pending_review'] ?? 0 }}</h3>
                    <small class="text-muted">Pending Review</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- My Tasks List --}}
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>My Assigned Tasks</h5>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            @if(isset($myTasks) && $myTasks->count() > 0)
                <div class="task-list">
                    @foreach($myTasks->take(8) as $task)
                        <div class="task-item d-flex align-items-center justify-content-between p-3 mb-2 rounded border-start border-4
                            @if($task->priority === 'urgent') border-danger bg-danger bg-opacity-10
                            @elseif($task->priority === 'high') border-warning bg-warning bg-opacity-10
                            @elseif($task->isOverdue()) border-danger
                            @else border-primary bg-light
                            @endif">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="fw-semibold text-decoration-none text-dark">
                                        {{ $task->title }}
                                    </a>
                                    @if($task->priority === 'urgent')
                                        <span class="badge bg-danger ms-2">Urgent</span>
                                    @elseif($task->priority === 'high')
                                        <span class="badge bg-warning text-dark ms-2">High</span>
                                    @endif
                                    @if($task->isOverdue())
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    <span class="me-3"><i class="fas fa-project-diagram me-1"></i>{{ $task->project->name ?? 'No Project' }}</span>
                                    @if($task->due_date)
                                        <span><i class="fas fa-calendar me-1"></i>{{ $task->due_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge
                                    @if($task->status === 'pending') bg-secondary
                                    @elseif($task->status === 'in_progress') bg-primary
                                    @elseif($task->status === 'review') bg-info
                                    @elseif($task->status === 'completed') bg-success
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                                @if($task->status === 'pending')
                                    <form action="{{ route('admin.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-sm btn-success" title="Start Task">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @elseif($task->status === 'in_progress')
                                    @if($task->requires_review)
                                        <form action="{{ route('admin.tasks.submit-review', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info" title="Submit for Review">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark Complete">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <p>No active tasks assigned to you.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Workload Widget --}}
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>My Workload</h5>
            <div class="text-center mb-3">
                <div class="position-relative d-inline-block" style="width: 120px; height: 120px;">
                    <svg viewBox="0 0 36 36" class="circular-chart
                        @if(($stats['utilization'] ?? 0) < 70) green
                        @elseif(($stats['utilization'] ?? 0) < 90) orange
                        @else red
                        @endif">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="circle" stroke-dasharray="{{ $stats['utilization'] ?? 0 }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <text x="18" y="20.35" class="percentage">{{ $stats['utilization'] ?? 0 }}%</text>
                    </svg>
                </div>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Allocated: {{ $stats['current_workload_hours'] ?? 0 }}h</span>
                <span>Available: {{ $stats['available_hours'] ?? 40 }}h</span>
            </div>
        </div>

        {{-- Pending My Review --}}
        @if(isset($pendingMyReview) && $pendingMyReview->count() > 0)
            <div class="dashboard-card mb-4">
                <h5 class="mb-3"><i class="fas fa-clipboard-check me-2"></i>Pending My Review</h5>
                @foreach($pendingMyReview->take(5) as $task)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none">
                                {{ Str::limit($task->title, 25) }}
                            </a>
                            <div class="small text-muted">by {{ $task->assignedTo->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <form action="{{ route('admin.tasks.approve', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-danger" title="Reject"
                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $task->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Awaiting Approval --}}
        @if(isset($mySubmittedForReview) && $mySubmittedForReview->count() > 0)
            <div class="dashboard-card mb-4">
                <h5 class="mb-3"><i class="fas fa-hourglass-half me-2"></i>Awaiting Approval</h5>
                @foreach($mySubmittedForReview->take(5) as $task)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none">
                                {{ Str::limit($task->title, 25) }}
                            </a>
                            <div class="small text-muted">
                                Reviewer: {{ $task->reviewedBy->name ?? 'Pending' }}
                            </div>
                        </div>
                        <span class="badge bg-info">In Review</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Recently Completed --}}
        @if(isset($recentlyCompleted) && $recentlyCompleted->count() > 0)
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-check-circle me-2 text-success"></i>Recently Completed</h5>
                @foreach($recentlyCompleted->take(5) as $task)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <span class="text-decoration-line-through text-muted">{{ Str::limit($task->title, 30) }}</span>
                            <div class="small text-muted">
                                {{ $task->completed_at ? $task->completed_at->diffForHumans() : '' }}
                            </div>
                        </div>
                        <i class="fas fa-check text-success"></i>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- 2-Week Calendar View --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <h5 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Upcoming Due Dates</h5>
            <div class="calendar-timeline d-flex overflow-auto pb-2">
                @php
                    $today = now();
                    $days = collect(range(0, 13))->map(fn($i) => $today->copy()->addDays($i));
                @endphp
                @foreach($days as $day)
                    @php
                        $dayTasks = isset($calendarTasks) ? ($calendarTasks[$day->format('Y-m-d')] ?? collect()) : collect();
                        $isToday = $day->isToday();
                        $isWeekend = $day->isWeekend();
                    @endphp
                    <div class="calendar-day flex-shrink-0 text-center p-2 me-2 rounded {{ $isToday ? 'bg-primary text-white' : ($isWeekend ? 'bg-light' : '') }}" style="min-width: 80px;">
                        <div class="small {{ $isToday ? '' : 'text-muted' }}">{{ $day->format('D') }}</div>
                        <div class="fw-bold">{{ $day->format('d') }}</div>
                        <div class="small {{ $isToday ? '' : 'text-muted' }}">{{ $day->format('M') }}</div>
                        @if($dayTasks->count() > 0)
                            <div class="mt-1">
                                <span class="badge {{ $isToday ? 'bg-white text-primary' : 'bg-primary' }}">
                                    {{ $dayTasks->count() }} task{{ $dayTasks->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .circular-chart {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        max-height: 120px;
    }
    .circle-bg {
        fill: none;
        stroke: #eee;
        stroke-width: 3.8;
    }
    .circle {
        fill: none;
        stroke-width: 2.8;
        stroke-linecap: round;
        animation: progress 1s ease-out forwards;
    }
    .circular-chart.green .circle { stroke: #28a745; }
    .circular-chart.orange .circle { stroke: #fd7e14; }
    .circular-chart.red .circle { stroke: #dc3545; }
    .percentage {
        fill: #666;
        font-family: sans-serif;
        font-size: 0.5em;
        text-anchor: middle;
    }
    @keyframes progress {
        0% { stroke-dasharray: 0 100; }
    }
    .calendar-timeline::-webkit-scrollbar {
        height: 6px;
    }
    .calendar-timeline::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    .task-item {
        transition: all 0.2s ease;
    }
    .task-item:hover {
        transform: translateX(5px);
    }
</style>
@endpush
