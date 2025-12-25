@extends('layouts.admin')

@section('title', 'Skills Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Skills Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Skills</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Skill
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
        <form action="{{ route('admin.skills.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search skills..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="domain" {{ request('category') == 'domain' ? 'selected' : '' }}>Domain</option>
                    <option value="soft_skill" {{ request('category') == 'soft_skill' ? 'selected' : '' }}>Soft Skill</option>
                    <option value="certification" {{ request('category') == 'certification' ? 'selected' : '' }}>Certification</option>
                </select>
            </div>
            <div class="col-md-3">
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

    <!-- Skills Table -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Category</th>
                        <th>Consultants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skills as $skill)
                        <tr>
                            <td>
                                <strong>{{ $skill->name }}</strong>
                                @if($skill->description)
                                    <br><small class="text-muted">{{ Str::limit($skill->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $skill->category === 'technical' ? 'primary' : ($skill->category === 'domain' ? 'info' : ($skill->category === 'certification' ? 'success' : 'secondary')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $skill->category)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-users me-1"></i>{{ $skill->users_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $skill->is_active ? 'success' : 'secondary' }}">
                                    {{ $skill->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.skills.edit', $skill) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $skill->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $skill->id }}" action="{{ route('admin.skills.destroy', $skill) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No skills found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($skills->hasPages())
            <div class="mt-4">
                {{ $skills->links() }}
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
                Are you sure you want to delete this skill? This action cannot be undone.
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
