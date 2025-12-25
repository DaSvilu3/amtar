{{-- Project Manager Dashboard - Project & Team Focused View --}}

{{-- Stats Row --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="fas fa-project-diagram fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['my_projects_count'] ?? 0 }}</h3>
                    <small class="text-muted">My Projects</small>
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
                    <h3 class="mb-0">{{ $stats['team_pending_tasks'] ?? 0 }}</h3>
                    <small class="text-muted">Pending Tasks</small>
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
                    <h3 class="mb-0">{{ $stats['pending_reviews'] ?? 0 }}</h3>
                    <small class="text-muted">Pending Reviews</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['overdue_tasks'] ?? 0 }}</h3>
                    <small class="text-muted">Overdue Tasks</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- My Projects --}}
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i>My Projects</h5>
                <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            @if(isset($myProjects) && $myProjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Project</th>
                                <th>Client</th>
                                <th>Progress</th>
                                <th>Tasks</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myProjects->take(6) as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.projects.show', $project) }}" class="text-decoration-none fw-semibold">
                                            {{ Str::limit($project->name, 30) }}
                                        </a>
                                        <div class="small text-muted">{{ $project->project_number }}</div>
                                    </td>
                                    <td>{{ $project->client->name ?? 'N/A' }}</td>
                                    <td style="width: 150px;">
                                        <div class="progress" style="height: 8px;">
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
                                        <small class="text-muted">{{ $project->progress }}%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $project->tasks->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($project->status === 'planning') bg-secondary
                                            @elseif($project->status === 'in_progress') bg-primary
                                            @elseif($project->status === 'on_hold') bg-warning text-dark
                                            @elseif($project->status === 'completed') bg-success
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-folder-plus fa-3x mb-3"></i>
                    <p>No projects assigned to you yet.</p>
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">Create Project</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Upcoming Milestones --}}
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-flag-checkered me-2"></i>Upcoming Milestones</h5>
            @if(isset($upcomingMilestones) && $upcomingMilestones->count() > 0)
                @foreach($upcomingMilestones as $milestone)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ Str::limit($milestone->title, 25) }}</div>
                            <div class="small text-muted">{{ $milestone->project->name ?? '' }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small {{ $milestone->target_date && $milestone->target_date->isPast() ? 'text-danger' : 'text-muted' }}">
                                {{ $milestone->target_date ? $milestone->target_date->format('M d') : 'No date' }}
                            </div>
                            <span class="badge
                                @if($milestone->status === 'pending') bg-secondary
                                @elseif($milestone->status === 'in_progress') bg-primary
                                @elseif($milestone->status === 'completed') bg-success
                                @else bg-danger
                                @endif" style="font-size: 0.7em;">
                                {{ ucfirst($milestone->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-3 text-muted">
                    <i class="fas fa-calendar-check mb-2"></i>
                    <p class="small mb-0">No upcoming milestones</p>
                </div>
            @endif
        </div>

        {{-- Team Workload --}}
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-users me-2"></i>Team Workload</h5>
            @if(isset($teamMembers) && $teamMembers->count() > 0)
                @foreach($teamMembers->take(5) as $member)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $member->name }}</div>
                                <div class="small text-muted">{{ $member->assigned_tasks_count }} tasks</div>
                            </div>
                        </div>
                        <div class="progress" style="width: 60px; height: 6px;">
                            @php
                                $workload = min(100, ($member->assigned_tasks_count / 5) * 100);
                            @endphp
                            <div class="progress-bar
                                @if($workload < 40) bg-success
                                @elseif($workload < 70) bg-warning
                                @else bg-danger
                                @endif"
                                style="width: {{ $workload }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-3 text-muted">
                    <p class="small mb-0">No team members on your projects</p>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="dashboard-card">
            <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.projects.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>New Project
                </a>
                <a href="{{ route('admin.tasks.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>New Task
                </a>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>New Client
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Team Tasks Overview --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Team Tasks</h5>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>

            @if(isset($teamTasks) && $teamTasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task</th>
                                <th>Project</th>
                                <th>Assigned To</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamTasks->take(10) as $task)
                                <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                                    <td>
                                        <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none">
                                            {{ Str::limit($task->title, 35) }}
                                        </a>
                                    </td>
                                    <td class="small">{{ $task->project->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 24px; height: 24px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                                {{ $task->assignedTo ? strtoupper(substr($task->assignedTo->name, 0, 1)) : '?' }}
                                            </div>
                                            <span class="small">{{ $task->assignedTo->name ?? 'Unassigned' }}</span>
                                        </div>
                                    </td>
                                    <td class="small {{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                        {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($task->priority === 'urgent') bg-danger
                                            @elseif($task->priority === 'high') bg-warning text-dark
                                            @elseif($task->priority === 'medium') bg-info
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($task->status === 'pending') bg-secondary
                                            @elseif($task->status === 'in_progress') bg-primary
                                            @elseif($task->status === 'review') bg-info
                                            @elseif($task->status === 'completed') bg-success
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-tasks fa-3x mb-3"></i>
                    <p>No active tasks in your projects.</p>
                </div>
            @endif
        </div>
    </div>
</div>
