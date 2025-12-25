@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Projects</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Project
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="dashboard-card mb-4">
        <form action="{{ route('admin.projects.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, number, or client..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach($statuses ?? [] as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Client</label>
                <select name="client_id" class="form-select">
                    <option value="">All Clients</option>
                    @foreach($clients ?? [] as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name ?? $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Project Manager</label>
                <select name="pm_id" class="form-select">
                    <option value="">All PMs</option>
                    @foreach($projectManagers ?? [] as $pm)
                        <option value="{{ $pm->id }}" {{ request('pm_id') == $pm->id ? 'selected' : '' }}>
                            {{ $pm->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'client_id', 'pm_id']))
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    <div class="row g-3 mb-4">
        @php
            $allProjects = $items->total();
            $inProgress = App\Models\Project::where('status', 'in_progress')->count();
            $completed = App\Models\Project::where('status', 'completed')->count();
            $overdue = App\Models\Project::where('end_date', '<', now())->where('status', '!=', 'completed')->count();
        @endphp
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="stat-icon bg-primary"><i class="fas fa-project-diagram"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $allProjects }}</div>
                    <div class="stat-label">Total Projects</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="stat-icon bg-info"><i class="fas fa-spinner"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $inProgress }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $completed }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini">
                <div class="stat-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-content">
                    <div class="stat-value">{{ $overdue }}</div>
                    <div class="stat-label">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 100px;">#</th>
                        <th>Project</th>
                        <th>Client</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 120px;">Progress</th>
                        <th>PM</th>
                        <th style="width: 100px;">Due Date</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items ?? [] as $project)
                        @php
                            $isOverdue = $project->end_date && $project->end_date->isPast() && $project->status !== 'completed';
                            $progress = $project->progress ?? $project->calculateTaskProgress();
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger-light' : '' }}">
                            <td>
                                <span class="text-muted small">{{ $project->project_number ?? 'PRJ-'.$project->id }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="fw-semibold text-decoration-none project-name-link">
                                    {{ $project->name }}
                                </a>
                                @if($project->mainService)
                                    <div class="small text-muted">
                                        <i class="fas fa-layer-group me-1"></i>{{ $project->mainService->name }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($project->client)
                                    <span class="text-dark">{{ $project->client->company_name ?? $project->client->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $project->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress-wrapper">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $progress >= 100 ? 'bg-success' : ($progress > 50 ? 'bg-primary' : 'bg-warning') }}"
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $progress }}%</small>
                                </div>
                            </td>
                            <td>
                                @if($project->projectManager)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm">{{ substr($project->projectManager->name, 0, 1) }}</div>
                                        <span class="small">{{ $project->projectManager->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($project->end_date)
                                    <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $project->end_date->format('M d, Y') }}
                                    </span>
                                    @if($isOverdue)
                                        <div class="small text-danger">
                                            <i class="fas fa-exclamation-circle"></i> Overdue
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $project->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $project->id }}" action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-project-diagram fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No projects found</p>
                                @if(request()->hasAny(['search', 'status', 'client_id', 'pm_id']))
                                    <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-primary mt-2">Clear filters</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($items) && $items->hasPages())
            <div class="p-3 border-top">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this project? This will also delete all associated tasks, milestones, and contracts.
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
    .stat-card-mini {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }

    .stat-card-mini .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .stat-card-mini .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
    }

    .stat-card-mini .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
    }

    .project-name-link {
        color: var(--primary-color);
    }

    .project-name-link:hover {
        color: var(--hover-color);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending { background: #e0e0e0; color: #616161; }
    .status-in_progress { background: #e3f2fd; color: #1976d2; }
    .status-on_hold { background: #fff3e0; color: #f57c00; }
    .status-completed { background: #e8f5e9; color: #388e3c; }
    .status-cancelled { background: #ffebee; color: #c62828; }

    .progress-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .progress-wrapper .progress {
        flex: 1;
        background: #e9ecef;
        border-radius: 3px;
    }

    .avatar-circle-sm {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--secondary-color);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .table-danger-light {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }

    .table-danger-light:hover {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    .table thead {
        background-color: var(--primary-color);
        color: white;
    }

    .table thead th {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 12px 15px;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
</style>
@endpush

@push('scripts')
<script>
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
</script>
@endpush
