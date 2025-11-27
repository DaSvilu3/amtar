@extends('layouts.admin')

@section('title', 'Milestones')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Milestones</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Milestones</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.milestones.create', request()->only('project_id')) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Milestone
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
        <form action="{{ route('admin.milestones.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <select name="project_id" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
            </div>
            @if(request('project_id'))
                <div class="col-md-2">
                    <form action="{{ route('admin.milestones.generate', request('project_id')) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-100" title="Generate milestones from project service stages">
                            <i class="fas fa-magic me-1"></i>Auto Generate
                        </button>
                    </form>
                </div>
            @endif
        </form>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Milestone</th>
                        <th>Project</th>
                        <th>Service Stage</th>
                        <th>Target Date</th>
                        <th>Progress</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items ?? [] as $item)
                        <tr class="{{ $item->isOverdue() ? 'table-danger' : '' }}">
                            <td><strong>{{ $item->title }}</strong></td>
                            <td>{{ $item->project->name ?? '-' }}</td>
                            <td>
                                @if($item->serviceStage)
                                    <span class="badge bg-light text-dark">{{ $item->serviceStage->name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->target_date)
                                    <span class="{{ $item->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                        {{ $item->target_date->format('M d, Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php $progress = $item->calculateProgress(); @endphp
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div class="progress-bar bg-{{ $item->status === 'completed' ? 'success' : 'primary' }}"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progress }}%</small>
                            </td>
                            <td>
                                @if($item->payment_percentage)
                                    {{ $item->payment_percentage }}%
                                @elseif($item->payment_amount)
                                    {{ number_format($item->payment_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $item->status === 'completed' ? 'success' : ($item->status === 'in_progress' ? 'primary' : ($item->status === 'overdue' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.milestones.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.milestones.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.milestones.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No milestones found</p>
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
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this milestone? This action cannot be undone.
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
