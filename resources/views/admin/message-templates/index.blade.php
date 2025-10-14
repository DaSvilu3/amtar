@extends('layouts.admin')

@section('title', 'Message Templates')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Message Templates</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Message Templates</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.message-templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Message Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="dashboard-card mb-4">
        <form action="{{ route('admin.message-templates.index') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search message templates..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items ?? [] as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td><strong>{{ $item->name ?? $item->title ?? 'N/A' }}</strong></td>
                            <td>{{ $item->created_at ? $item->created_at->format('M d, Y') : '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.message-templates.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.message-templates.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.message-templates.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No message templates found</p>
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
                Are you sure you want to delete this message template? This action cannot be undone.
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
