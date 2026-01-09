@extends('layouts.admin')

@section('title', 'Tasks')

@section('content')
<div class="fade-in" id="tasksContainer">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Tasks</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tasks</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- Auto-refresh toggle -->
            <div class="form-check form-switch me-3">
                <input class="form-check-input" type="checkbox" id="autoRefreshToggle" onchange="toggleAutoRefresh()">
                <label class="form-check-label small" for="autoRefreshToggle">
                    Auto-refresh <span id="refreshCountdown" class="badge bg-secondary ms-1" style="display: none;">30s</span>
                </label>
            </div>

            <div class="btn-group" role="group">
                <a href="{{ route('admin.tasks.index', array_merge(request()->query(), ['view' => 'list'])) }}"
                   class="btn btn-outline-primary {{ $viewType === 'list' ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                </a>
                <a href="{{ route('admin.tasks.index', array_merge(request()->query(), ['view' => 'kanban'])) }}"
                   class="btn btn-outline-primary {{ $viewType === 'kanban' ? 'active' : '' }}">
                    <i class="fas fa-columns"></i>
                </a>
            </div>
            @can('create', App\Models\Task::class)
            <button type="button" class="btn btn-outline-success" id="batchAssignBtn" style="display: none;" onclick="batchAssignSelected()">
                <i class="fas fa-user-plus me-1"></i>Auto-Assign Selected
            </button>
            <button type="button" class="btn btn-outline-danger" id="batchDeleteBtn" style="display: none;" onclick="confirmBatchDelete()">
                <i class="fas fa-trash me-1"></i>Delete Selected
            </button>
            <a href="{{ route('admin.tasks.create', request()->only('project_id')) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Task
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Cards for Critical Items -->
    @if(auth()->user()->hasAnyRole(['administrator', 'project-manager']))
    @php
        $overdueCount = $taskStats['overdue'] ?? 0;
        $urgentCount = $taskStats['urgent_high'] ?? 0;
        $unassignedCount = $taskStats['unassigned'] ?? 0;
        $dueThisWeek = $taskStats['due_this_week'] ?? 0;
    @endphp
    @if($overdueCount > 0 || $urgentCount > 0 || $unassignedCount > 0)
    <div class="row mb-4">
        @if($overdueCount > 0)
        <div class="col-md-4">
            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                <div class="alert-icon me-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $overdueCount }} Overdue Task{{ $overdueCount > 1 ? 's' : '' }}</h6>
                    <small>Require immediate attention</small>
                </div>
                <a href="{{ route('admin.tasks.index', ['overdue_only' => 1, 'view' => $viewType]) }}" class="btn btn-sm btn-danger">
                    View
                </a>
            </div>
        </div>
        @endif

        @if($urgentCount > 0)
        <div class="col-md-4">
            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                <div class="alert-icon me-3">
                    <i class="fas fa-fire fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $urgentCount }} High Priority</h6>
                    <small>Urgent or high priority tasks</small>
                </div>
                <a href="{{ route('admin.tasks.index', ['priority' => ['urgent', 'high'], 'view' => $viewType]) }}" class="btn btn-sm btn-warning">
                    View
                </a>
            </div>
        </div>
        @endif

        @if($unassignedCount > 0)
        <div class="col-md-4">
            <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                <div class="alert-icon me-3">
                    <i class="fas fa-user-slash fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $unassignedCount }} Unassigned</h6>
                    <small>Tasks need to be assigned</small>
                </div>
                <a href="{{ route('admin.tasks.index', ['assigned_to' => 'unassigned', 'view' => $viewType]) }}" class="btn btn-sm btn-info">
                    View
                </a>
            </div>
        </div>
        @endif
    </div>
    @endif
    @endif

    <!-- Advanced Filters -->
    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    <i class="fas fa-times me-1"></i>Clear All
                </button>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                    <i class="fas fa-sliders-h me-1"></i>Advanced
                </button>
            </div>
        </div>

        <form action="{{ route('admin.tasks.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="view" value="{{ $viewType }}">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Projects</label>
                    <select name="project_id[]" class="form-select select2-multi" multiple data-placeholder="All Projects">
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ in_array($project->id, (array) request('project_id', [])) ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status[]" class="form-select select2-multi" multiple data-placeholder="All Statuses">
                        @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'review' => 'Review', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" {{ in_array($value, (array) request('status', [])) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Priority</label>
                    <select name="priority[]" class="form-select select2-multi" multiple data-placeholder="All Priorities">
                        @foreach(['urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $value => $label)
                            <option value="{{ $value }}" {{ in_array($value, (array) request('priority', [])) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Assigned To</label>
                    <select name="assigned_to[]" class="form-select select2-multi" multiple data-placeholder="All Assignees">
                        <option value="unassigned" {{ in_array('unassigned', (array) request('assigned_to', [])) ? 'selected' : '' }}>
                            Unassigned
                        </option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, (array) request('assigned_to', [])) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (Collapsed) -->
            <div class="collapse mt-3" id="advancedFilters">
                <div class="row g-3 pt-3 border-top">
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Due From</label>
                        <input type="date" name="due_from" class="form-control" value="{{ request('due_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Due To</label>
                        <input type="date" name="due_to" class="form-control" value="{{ request('due_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">&nbsp;</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="overdue_only" value="1" id="overdueOnly" {{ request('overdue_only') ? 'checked' : '' }}>
                            <label class="form-check-label" for="overdueOnly">
                                Overdue only
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards & Charts -->
    @if(auth()->user()->hasAnyRole(['administrator', 'project-manager']))
    <div class="row mb-4">
        <!-- Quick Stats -->
        <div class="col-lg-2">
            <div class="dashboard-card stats-card">
                <h6 class="text-muted mb-3"><i class="fas fa-chart-bar me-2"></i>Quick Stats</h6>
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="mb-0 text-primary">{{ array_sum($taskStats['by_status'] ?? []) }}</h3>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="mb-0 text-warning">{{ $taskStats['by_status']['pending'] ?? 0 }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-6">
                        <h3 class="mb-0 text-info">{{ $taskStats['by_status']['in_progress'] ?? 0 }}</h3>
                        <small class="text-muted">In Progress</small>
                    </div>
                    <div class="col-6">
                        <h3 class="mb-0 text-success">{{ $taskStats['by_status']['completed'] ?? 0 }}</h3>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-lg-3">
            <div class="dashboard-card stats-card">
                <h6 class="text-muted mb-3"><i class="fas fa-pie-chart me-2"></i>Status Distribution</h6>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Priority Distribution Chart -->
        <div class="col-lg-3">
            <div class="dashboard-card stats-card">
                <h6 class="text-muted mb-3"><i class="fas fa-chart-pie me-2"></i>Priority Distribution</h6>
                <div class="chart-container">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Workload Chart -->
        <div class="col-lg-4">
            <div class="dashboard-card stats-card workload-card">
                <div class="workload-scroll">
                    @include('admin.tasks.partials._workload-chart')
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($viewType === 'kanban')
        <!-- Kanban Board View -->
        <div class="row">
            @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'review' => 'Review', 'completed' => 'Completed'] as $status => $label)
                <div class="col-md-3">
                    <div class="dashboard-card kanban-column" data-status="{{ $status }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <span class="badge bg-{{ $status === 'completed' ? 'success' : ($status === 'in_progress' ? 'primary' : ($status === 'review' ? 'warning' : 'secondary')) }}">
                                    {{ $label }}
                                </span>
                            </h6>
                            <span class="badge bg-light text-dark">{{ count($tasks[$status] ?? []) }}</span>
                        </div>
                        <div class="kanban-tasks" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                            @foreach($tasks[$status] ?? [] as $task)
                                <div class="card mb-2 task-card {{ $task->isOverdue() ? 'border-danger' : '' }} {{ $task->priority === 'urgent' ? 'pulse-urgent' : '' }}" data-task-id="{{ $task->id }}">
                                    <div class="card-body p-2">
                                        @if($task->isOverdue())
                                            <div class="badge bg-danger mb-1 w-100"><i class="fas fa-clock me-1"></i>OVERDUE</div>
                                        @endif
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}" style="font-size: 10px;">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                            <small class="text-muted text-truncate" style="max-width: 80px;">{{ $task->project->name ?? '' }}</small>
                                        </div>
                                        <h6 class="mt-2 mb-1" style="font-size: 13px;">
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none text-dark">
                                                {{ $task->title }}
                                            </a>
                                        </h6>
                                        @if($task->projectService)
                                            <small class="text-muted d-block">{{ $task->projectService->service->name ?? '' }}</small>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            @if($task->assignedTo)
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $task->assignedTo->name }}
                                                </small>
                                            @else
                                                <span class="badge bg-light text-warning"><i class="fas fa-user-slash"></i> Unassigned</span>
                                            @endif
                                            @if($task->due_date)
                                                <small class="{{ $task->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    <i class="fas fa-clock"></i> {{ $task->due_date->format('M d') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- List View -->
        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            @can('create', App\Models\Task::class)
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="selectAllTasks" onchange="toggleSelectAll(this)">
                            </th>
                            @endcan
                            <th>Task</th>
                            <th>Project</th>
                            <th>Service</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items ?? [] as $item)
                            <tr class="{{ $item->isOverdue() ? 'table-danger' : ($item->priority === 'urgent' ? 'table-warning' : '') }}">
                                @can('create', App\Models\Task::class)
                                <td>
                                    <input type="checkbox" class="form-check-input task-select-checkbox" value="{{ $item->id }}" onchange="updateBatchButtons()">
                                </td>
                                @endcan
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->isOverdue())
                                            <span class="badge bg-danger me-2" title="Overdue"><i class="fas fa-exclamation-triangle"></i></span>
                                        @endif
                                        <div>
                                            <strong>{{ $item->title }}</strong>
                                            @if($item->isBlocked())
                                                <span class="badge bg-warning ms-1" title="Blocked by dependencies">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                            @if($item->estimated_hours)
                                                <br><small class="text-muted"><i class="fas fa-clock"></i> {{ $item->estimated_hours }}h est.</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.projects.show', $item->project_id) }}" class="text-decoration-none">
                                        {{ $item->project->name ?? '-' }}
                                    </a>
                                </td>
                                <td><small>{{ $item->projectService->service->name ?? '-' }}</small></td>
                                <td>
                                    @if($item->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                {{ strtoupper(substr($item->assignedTo->name, 0, 2)) }}
                                            </div>
                                            {{ $item->assignedTo->name }}
                                        </div>
                                    @else
                                        <span class="badge bg-light text-warning">
                                            <i class="fas fa-user-slash me-1"></i>Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->priority === 'urgent' ? 'danger' : ($item->priority === 'high' ? 'warning' : ($item->priority === 'medium' ? 'info' : 'secondary')) }}">
                                        @if($item->priority === 'urgent')<i class="fas fa-fire me-1"></i>@endif
                                        {{ ucfirst($item->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'completed' ? 'success' : ($item->status === 'in_progress' ? 'primary' : ($item->status === 'review' ? 'warning' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->due_date)
                                        <span class="{{ $item->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                            {{ $item->due_date->format('M d, Y') }}
                                            @if($item->isOverdue())
                                                <br><small class="text-danger">{{ $item->due_date->diffForHumans() }}</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tasks.show', $item->id) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $item)
                                        <a href="{{ route('admin.tasks.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @if(!$item->assigned_to)
                                        <button type="button" class="btn btn-sm btn-success" onclick="autoAssignTask({{ $item->id }})" title="Auto-Assign">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        @endif
                                        @can('delete', $item)
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.tasks.destroy', $item->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No tasks found</p>
                                    <small class="text-muted">Try adjusting your filters or create a new task</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($items) && $items->hasPages())
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} tasks
                    </small>
                    {{ $items->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Batch Delete Modal -->
<div class="modal fade" id="batchDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Batch Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="batchDeleteCount">0</strong> selected tasks?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBatchDeleteBtn">
                    <i class="fas fa-trash me-1"></i>Delete All Selected
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .table thead { background-color: var(--primary-color); color: white; }
    .kanban-column { background: #f8f9fa; }
    .task-card { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
    .task-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* Stats cards with fixed height */
    .stats-card {
        height: 200px;
        overflow: hidden;
    }

    .chart-container {
        height: 130px;
        width: 100%;
        position: relative;
    }

    .chart-container canvas {
        max-height: 100%;
        max-width: 100%;
    }

    .workload-card {
        padding: 0 !important;
    }

    .workload-scroll {
        height: 200px;
        overflow-y: auto;
        padding: 1rem;
    }

    .workload-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .workload-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .workload-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    .avatar-sm {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--secondary-color);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    .alert-icon {
        opacity: 0.8;
    }

    @keyframes pulse-urgent {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    }

    .pulse-urgent {
        animation: pulse-urgent 2s infinite;
    }

    .kanban-tasks::-webkit-scrollbar {
        width: 6px;
    }

    .kanban-tasks::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .kanban-tasks::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    .kanban-tasks::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Initialize Select2
    document.addEventListener('DOMContentLoaded', function() {
        $('.select2-multi').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            closeOnSelect: false
        });

        // Initialize charts with slight delay to ensure Chart.js is ready
        setTimeout(initCharts, 100);
    });

    // Charts initialization
    function initCharts() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }

        const statusData = @json($taskStats['by_status'] ?? []);
        const priorityData = @json($taskStats['by_priority'] ?? []);

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusValues = [
                statusData['pending'] || 0,
                statusData['in_progress'] || 0,
                statusData['review'] || 0,
                statusData['completed'] || 0
            ];
            // Only render chart if there's data
            const hasStatusData = statusValues.some(v => v > 0);
            if (hasStatusData) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'In Progress', 'Review', 'Completed'],
                        datasets: [{
                            data: statusValues,
                            backgroundColor: ['#6c757d', '#0d6efd', '#ffc107', '#198754'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 8, font: { size: 10 } }
                            }
                        },
                        cutout: '60%'
                    }
                });
            } else {
                statusCtx.parentElement.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-chart-pie fa-2x mb-2 opacity-50"></i><p class="small mb-0">No data</p></div>';
            }
        }

        // Priority Chart
        const priorityCtx = document.getElementById('priorityChart');
        if (priorityCtx) {
            const priorityValues = [
                priorityData['urgent'] || 0,
                priorityData['high'] || 0,
                priorityData['medium'] || 0,
                priorityData['low'] || 0
            ];
            // Only render chart if there's data
            const hasPriorityData = priorityValues.some(v => v > 0);
            if (hasPriorityData) {
                new Chart(priorityCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Urgent', 'High', 'Medium', 'Low'],
                        datasets: [{
                            data: priorityValues,
                            backgroundColor: ['#dc3545', '#fd7e14', '#17a2b8', '#6c757d'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 8, font: { size: 10 } }
                            }
                        },
                        cutout: '60%'
                    }
                });
            } else {
                priorityCtx.parentElement.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-chart-pie fa-2x mb-2 opacity-50"></i><p class="small mb-0">No data</p></div>';
            }
        }
    }

    // Auto-refresh functionality
    let autoRefreshInterval = null;
    let countdown = 30;

    function toggleAutoRefresh() {
        const toggle = document.getElementById('autoRefreshToggle');
        const countdownBadge = document.getElementById('refreshCountdown');

        if (toggle.checked) {
            countdownBadge.style.display = 'inline';
            countdown = 30;
            updateCountdown();
            autoRefreshInterval = setInterval(() => {
                countdown--;
                updateCountdown();
                if (countdown <= 0) {
                    location.reload();
                }
            }, 1000);
        } else {
            countdownBadge.style.display = 'none';
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }
    }

    function updateCountdown() {
        document.getElementById('refreshCountdown').textContent = countdown + 's';
    }

    // Clear all filters
    function clearFilters() {
        const form = document.getElementById('filterForm');
        form.querySelectorAll('select').forEach(select => {
            $(select).val(null).trigger('change');
        });
        form.querySelectorAll('input[type="date"]').forEach(input => {
            input.value = '';
        });
        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        form.submit();
    }

    // Delete functionality
    let deleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    function confirmDelete(id) {
        deleteId = id;
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteId) {
            document.getElementById('delete-form-' + deleteId).submit();
        }
    });

    // Batch selection
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.task-select-checkbox').forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBatchButtons();
    }

    function updateBatchButtons() {
        const selectedCheckboxes = document.querySelectorAll('.task-select-checkbox:checked');
        const selectedCount = selectedCheckboxes.length;
        const batchAssignBtn = document.getElementById('batchAssignBtn');
        const batchDeleteBtn = document.getElementById('batchDeleteBtn');

        // Count unassigned selected tasks
        let unassignedCount = 0;
        selectedCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row && row.querySelector('.badge.text-warning')) {
                unassignedCount++;
            }
        });

        if (batchAssignBtn) {
            if (unassignedCount > 0) {
                batchAssignBtn.style.display = 'inline-block';
                batchAssignBtn.innerHTML = `<i class="fas fa-user-plus me-1"></i>Auto-Assign (${unassignedCount})`;
            } else {
                batchAssignBtn.style.display = 'none';
            }
        }

        if (batchDeleteBtn) {
            if (selectedCount > 0) {
                batchDeleteBtn.style.display = 'inline-block';
                batchDeleteBtn.innerHTML = `<i class="fas fa-trash me-1"></i>Delete (${selectedCount})`;
            } else {
                batchDeleteBtn.style.display = 'none';
            }
        }
    }

    // Batch auto-assign
    function batchAssignSelected() {
        const selectedIds = Array.from(document.querySelectorAll('.task-select-checkbox:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one task');
            return;
        }

        const btn = document.getElementById('batchAssignBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Assigning...';

        fetch('{{ route("admin.tasks.batch-assign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ task_ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.message || 'Assignment failed');
                btn.disabled = false;
                updateBatchButtons();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while assigning tasks');
            btn.disabled = false;
            updateBatchButtons();
        });
    }

    // Batch delete
    const batchDeleteModal = new bootstrap.Modal(document.getElementById('batchDeleteModal'));

    function confirmBatchDelete() {
        const selectedCount = document.querySelectorAll('.task-select-checkbox:checked').length;
        document.getElementById('batchDeleteCount').textContent = selectedCount;
        batchDeleteModal.show();
    }

    document.getElementById('confirmBatchDeleteBtn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.task-select-checkbox:checked')).map(cb => cb.value);

        // Submit delete requests sequentially
        const deletePromises = selectedIds.map(id => {
            return fetch(`/admin/tasks/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
        });

        Promise.all(deletePromises)
            .then(() => {
                showAlert('success', `${selectedIds.length} tasks deleted successfully`);
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while deleting tasks');
            });

        batchDeleteModal.hide();
    });

    // Single task auto-assign
    function autoAssignTask(taskId) {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`/admin/tasks/${taskId}/auto-assign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `Task assigned to ${data.assigned_to}`);
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('warning', data.message || 'No suitable assignee found');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    // Show alert helper
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'times-circle')} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        const container = document.querySelector('.fade-in');
        const firstCard = container.querySelector('.dashboard-card, .alert');
        if (firstCard) {
            firstCard.insertAdjacentHTML('beforebegin', alertHtml);
        } else {
            container.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }
</script>
@endpush
