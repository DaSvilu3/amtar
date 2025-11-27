@extends('layouts.admin')

@section('title', 'Service Stages')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Service Stages</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">Stages</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.stages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Stage
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

    <div class="dashboard-card">
        <p class="text-muted mb-4">
            Service stages represent the different phases of project delivery (e.g., Planning, Design, Review, etc.).
            Each service belongs to a specific stage.
        </p>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Services</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stages as $stage)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $stage->sort_order ?? '-' }}</span>
                            </td>
                            <td>
                                <strong>{{ $stage->name }}</strong>
                            </td>
                            <td><code>{{ $stage->slug }}</code></td>
                            <td>{{ Str::limit($stage->description, 50) ?? '-' }}</td>
                            <td><span class="badge bg-primary">{{ $stage->services_count }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.services.stages.edit', $stage) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $stage->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $stage->id }}" action="{{ route('admin.services.stages.destroy', $stage) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No service stages found</p>
                                <a href="{{ route('admin.services.stages.create') }}" class="btn btn-primary">Create First Stage</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stages->hasPages())
            <div class="mt-4">{{ $stages->links() }}</div>
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
                Are you sure you want to delete this stage? This action cannot be undone.
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
