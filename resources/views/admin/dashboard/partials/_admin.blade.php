{{-- Administrator Dashboard - System Overview --}}

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="fas fa-project-diagram fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['active_projects'] ?? 0 }}</h3>
                    <small class="text-muted">Active Projects</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                    <i class="fas fa-tasks fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['pending_tasks'] ?? 0 }}</h3>
                    <small class="text-muted">Pending Tasks</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_clients'] ?? 0 }}</h3>
                    <small class="text-muted">Total Clients</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                    <i class="fas fa-dollar-sign fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['monthly_revenue'] ?? 0) }}</h3>
                    <small class="text-muted">Monthly Revenue</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Stats --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h4>
                    <small class="text-muted">Total Users</small>
                </div>
                <span class="badge bg-success">{{ $stats['active_users'] ?? 0 }} active</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100 border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">{{ $stats['overdue_tasks'] ?? 0 }}</h4>
                    <small class="text-muted">Overdue Tasks</small>
                </div>
                <i class="fas fa-exclamation-triangle text-danger"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100 border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">{{ $stats['pending_reviews'] ?? 0 }}</h4>
                    <small class="text-muted">Pending Reviews</small>
                </div>
                <i class="fas fa-clipboard-check text-info"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">{{ $stats['completed_this_month'] ?? 0 }}</h4>
                    <small class="text-muted">Completed This Month</small>
                </div>
                <i class="fas fa-check-circle text-success"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Project Progress --}}
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Project Progress</h5>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            @if(isset($projectProgress) && $projectProgress->count() > 0)
                @foreach($projectProgress as $project)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <a href="{{ route('admin.projects.show', $project) }}" class="text-decoration-none fw-semibold">
                                {{ Str::limit($project->name, 40) }}
                            </a>
                            <span class="small text-muted">{{ $project->progress }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar
                                @if($project->progress < 25) bg-danger
                                @elseif($project->progress < 50) bg-warning
                                @elseif($project->progress < 75) bg-info
                                @else bg-success
                                @endif"
                                role="progressbar"
                                style="width: {{ $project->progress }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <p>No active projects to display.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Users by Role --}}
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-user-tag me-2"></i>Users by Role</h5>
            @if(isset($usersByRole) && $usersByRole->count() > 0)
                @foreach($usersByRole as $role)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span>{{ $role->name }}</span>
                        <span class="badge bg-primary">{{ $role->users_count }}</span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-3 text-muted">
                    <p class="small mb-0">No roles configured</p>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>New Project
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-user-plus me-2"></i>New User
                </a>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>New Client
                </a>
            </div>
        </div>

        {{-- System Dashboards --}}
        <div class="dashboard-card">
            <h5 class="mb-3"><i class="fas fa-tachometer-alt me-2"></i>Dashboards</h5>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.dashboards.finance') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="fas fa-dollar-sign me-3 text-success"></i>
                    Finance Dashboard
                </a>
                <a href="{{ route('admin.dashboards.projects') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="fas fa-project-diagram me-3 text-primary"></i>
                    Projects Dashboard
                </a>
                <a href="{{ route('admin.dashboards.hr') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="fas fa-users me-3 text-info"></i>
                    HR Dashboard
                </a>
                <a href="{{ route('admin.dashboards.performance') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <i class="fas fa-chart-line me-3 text-warning"></i>
                    Performance Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Recent Tasks --}}
<div class="row mt-4">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Tasks</h5>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            @if(isset($recentTasks) && $recentTasks->count() > 0)
                @foreach($recentTasks->take(5) as $task)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="flex-grow-1">
                            <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none fw-semibold">
                                {{ Str::limit($task->title, 30) }}
                            </a>
                            <div class="small text-muted">
                                {{ $task->project->name ?? 'No Project' }} -
                                {{ $task->assignedTo->name ?? 'Unassigned' }}
                            </div>
                        </div>
                        <span class="badge
                            @if($task->status === 'pending') bg-secondary
                            @elseif($task->status === 'in_progress') bg-primary
                            @elseif($task->status === 'review') bg-info
                            @elseif($task->status === 'completed') bg-success
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-3 text-muted">
                    <p class="small mb-0">No recent tasks</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card h-100">
            <h5 class="mb-3"><i class="fas fa-history me-2"></i>Recent Activities</h5>

            @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="activity-timeline">
                    @foreach(collect($recentActivities)->take(6) as $activity)
                        <div class="d-flex mb-3">
                            <div class="activity-icon me-3">
                                <div class="rounded-circle p-2
                                    @if($activity['type'] === 'project') bg-primary bg-opacity-10 text-primary
                                    @elseif($activity['type'] === 'task') bg-warning bg-opacity-10 text-warning
                                    @elseif($activity['type'] === 'contract') bg-success bg-opacity-10 text-success
                                    @else bg-info bg-opacity-10 text-info
                                    @endif">
                                    <i class="fas
                                        @if($activity['type'] === 'project') fa-project-diagram
                                        @elseif($activity['type'] === 'task') fa-tasks
                                        @elseif($activity['type'] === 'contract') fa-file-contract
                                        @else fa-info-circle
                                        @endif fa-sm"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small">{{ $activity['description'] ?? 'Activity' }}</div>
                                <div class="small text-muted">{{ $activity['time'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <p class="small mb-0">No recent activities</p>
                </div>
            @endif
        </div>
    </div>
</div>
