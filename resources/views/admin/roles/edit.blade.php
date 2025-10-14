@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-title mb-4">
        <h1>Edit Role</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $role->name ?? '') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3">{{ old('description', $role->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="permissions" class="form-label">Permissions (JSON) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('permissions') is-invalid @enderror"
                                  id="permissions"
                                  name="permissions"
                                  rows="10"
                                  required>{{ old('permissions', is_string($role->permissions ?? '') ? $role->permissions : json_encode($role->permissions ?? [], JSON_PRETTY_PRINT)) }}</textarea>
                        @error('permissions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <strong>JSON Format Example:</strong><br>
                            <code>["users.view", "users.create", "users.edit", "users.delete", "roles.view"]</code>
                        </small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Role name must be unique
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-check text-success me-2"></i>Permissions must be valid JSON array
                </p>
            </div>

            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-users me-2"></i>Users with this Role</h5>
                <p class="h3 mb-0">{{ $role->users_count ?? 0 }}</p>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $role->created_at ? $role->created_at->format('M d, Y H:i') : '-' }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $role->updated_at ? $role->updated_at->format('M d, Y H:i') : '-' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }
    .form-label {
        font-weight: 500;
        color: var(--primary-color);
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple JSON validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const permissionsField = document.getElementById('permissions');
        try {
            JSON.parse(permissionsField.value);
        } catch (error) {
            e.preventDefault();
            alert('Invalid JSON format in permissions field. Please check your syntax.');
            permissionsField.focus();
        }
    });
</script>
@endpush
