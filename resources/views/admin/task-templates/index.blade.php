@extends('layouts.admin')

@section('title', 'Task Templates')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Task Templates</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Task Templates</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.task-templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Template
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
        <form action="{{ route('admin.task-templates.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search templates..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="service_id" class="form-select">
                    <option value="">All Services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="service_stage_id" class="form-select">
                    <option value="">All Stages</option>
                    @foreach($stages as $stage)
                        <option value="{{ $stage->id }}" {{ request('service_stage_id') == $stage->id ? 'selected' : '' }}>
                            {{ $stage->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Templates Table -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Template</th>
                        <th>Service</th>
                        <th>Stage</th>
                        <th>Priority</th>
                        <th>Est. Hours</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->title }}</strong>
                                @if($template->description)
                                    <br><small class="text-muted">{{ Str::limit($template->description, 40) }}</small>
                                @endif
                            </td>
                            <td>{{ $template->service->name ?? '-' }}</td>
                            <td>
                                @if($template->serviceStage)
                                    <span class="badge bg-info">{{ $template->serviceStage->name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $template->priority === 'urgent' ? 'danger' : ($template->priority === 'high' ? 'warning' : ($template->priority === 'medium' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($template->priority) }}
                                </span>
                            </td>
                            <td>{{ $template->estimated_hours ?? '-' }}h</td>
                            <td>
                                @if($template->requires_review)
                                    <span class="badge bg-warning"><i class="fas fa-check"></i> Required</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.task-templates.show', $template) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.task-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $template->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $template->id }}" action="{{ route('admin.task-templates.destroy', $template) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No task templates found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this template? This action cannot be undone.
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
</script>
@endpush
