@extends('layouts.admin')

@section('title', 'Sub Services')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Sub Services</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">Sub Services</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.sub.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Sub Service
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="dashboard-card mb-4">
        <form action="{{ route('admin.services.sub.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Filter by Main Service</label>
                <select name="main_service_id" class="form-select">
                    <option value="">All Main Services</option>
                    @foreach($mainServices as $main)
                        <option value="{{ $main->id }}" {{ request('main_service_id') == $main->id ? 'selected' : '' }}>
                            {{ $main->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('admin.services.sub.index') }}" class="btn btn-light">Clear</a>
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Name</th>
                        <th>Main Service</th>
                        <th>Packages</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subServices as $service)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $service->sort_order ?? '-' }}</span>
                            </td>
                            <td>
                                <strong>{{ $service->name }}</strong>
                                <br><small class="text-muted">{{ $service->slug }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $service->mainService->name ?? '-' }}</span>
                            </td>
                            <td><span class="badge bg-info">{{ $service->service_packages_count }}</span></td>
                            <td><span class="badge bg-primary">{{ $service->projects_count }}</span></td>
                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.services.sub.edit', $service) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $service->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $service->id }}" action="{{ route('admin.services.sub.destroy', $service) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-code-branch fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No sub services found</p>
                                <a href="{{ route('admin.services.sub.create') }}" class="btn btn-primary">Create First Sub Service</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subServices->hasPages())
            <div class="mt-4">{{ $subServices->links() }}</div>
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
                Are you sure you want to delete this sub service? This action cannot be undone.
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
