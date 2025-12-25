@extends('layouts.admin')

@section('title', 'Tasks')

@section('content')
<div class="fade-in">
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
        <div class="d-flex gap-2">
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

    <!-- Filters -->
    <div class="dashboard-card mb-4">
        <form action="{{ route('admin.tasks.index') }}" method="GET" class="row g-3">
            <input type="hidden" name="view" value="{{ $viewType }}">
            <div class="col-md-3">
                <select name="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="assigned_to" class="form-select">
                    <option value="">All Assignees</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Workload Chart (visible for admins/PMs) -->
    @if(auth()->user()->hasAnyRole(['administrator', 'project-manager']))
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="dashboard-card">
                @include('admin.tasks.partials._workload-chart')
            </div>
        </div>
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="row">
                    <div class="col-md-3 text-center border-end">
                        <h4 class="mb-0 text-primary">{{ $items?->total() ?? ($tasks ? collect($tasks)->flatten()->count() : 0) }}</h4>
                        <small class="text-muted">Total Tasks</small>
                    </div>
                    <div class="col-md-3 text-center border-end">
                        <h4 class="mb-0 text-warning">{{ $items?->where('status', 'pending')->count() ?? ($tasks['pending'] ?? collect())->count() }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-md-3 text-center border-end">
                        <h4 class="mb-0 text-danger">{{ $items?->filter(fn($t) => $t->isOverdue())->count() ?? 0 }}</h4>
                        <small class="text-muted">Overdue</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4 class="mb-0 text-muted">{{ $items?->whereNull('assigned_to')->count() ?? 0 }}</h4>
                        <small class="text-muted">Unassigned</small>
                    </div>
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
                        <div class="kanban-tasks" style="min-height: 400px;">
                            @foreach($tasks[$status] ?? [] as $task)
                                <div class="card mb-2 task-card" data-task-id="{{ $task->id }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}" style="font-size: 10px;">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                            <small class="text-muted">{{ $task->project->name ?? '' }}</small>
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
                                                <small class="text-muted">Unassigned</small>
                                            @endif
                                            @if($task->due_date)
                                                <small class="{{ $task->isOverdue() ? 'text-danger' : 'text-muted' }}">
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
                <table class="table table-hover align-middle">
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
                            <tr class="{{ $item->isOverdue() ? 'table-danger' : '' }}">
                                @can('create', App\Models\Task::class)
                                <td>
                                    @if(!$item->assigned_to)
                                    <input type="checkbox" class="form-check-input task-select-checkbox" value="{{ $item->id }}" onchange="updateBatchButton()">
                                    @endif
                                </td>
                                @endcan
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    @if($item->isBlocked())
                                        <span class="badge bg-warning ms-1" title="Blocked by dependencies">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $item->project->name ?? '-' }}</td>
                                <td>{{ $item->projectService->service->name ?? '-' }}</td>
                                <td>
                                    @if($item->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 8px;">
                                                {{ strtoupper(substr($item->assignedTo->name, 0, 2)) }}
                                            </div>
                                            {{ $item->assignedTo->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->priority === 'urgent' ? 'danger' : ($item->priority === 'high' ? 'warning' : ($item->priority === 'medium' ? 'info' : 'secondary')) }}">
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
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tasks.show', $item->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.tasks.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.tasks.destroy', $item->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No tasks found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($items) && $items->hasPages())
                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this task? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .table thead { background-color: var(--primary-color); color: white; }
    .kanban-column { background: #f8f9fa; }
    .task-card { cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
    .task-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
@endpush

@push('scripts')
<script>
    let deleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    function confirmDelete(id) { deleteId = id; deleteModal.show(); }
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteId) document.getElementById('delete-form-' + deleteId).submit();
    });

    // Batch assignment functions
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.task-select-checkbox').forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateBatchButton();
    }

    function updateBatchButton() {
        const selectedCount = document.querySelectorAll('.task-select-checkbox:checked').length;
        const batchBtn = document.getElementById('batchAssignBtn');
        if (batchBtn) {
            if (selectedCount > 0) {
                batchBtn.style.display = 'inline-block';
                batchBtn.innerHTML = `<i class="fas fa-user-plus me-1"></i>Auto-Assign (${selectedCount})`;
            } else {
                batchBtn.style.display = 'none';
            }
        }
    }

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
                // Show success message
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.fade-in').insertBefore(successAlert, document.querySelector('.dashboard-card'));

                // Reload page after a short delay
                setTimeout(() => location.reload(), 1500);
            } else {
                alert(data.message || 'Assignment failed');
                btn.disabled = false;
                updateBatchButton();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning tasks');
            btn.disabled = false;
            updateBatchButton();
        });
    }
</script>
@endpush
