@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-title mb-4">
        <h1>Create New Role</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
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
                                  rows="3">{{ old('description') }}</textarea>
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
                                  required>{{ old('permissions', '[]') }}</textarea>
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
                            <i class="fas fa-save me-2"></i>Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>All fields marked with * are required
                </p>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Role name must be unique
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-check text-success me-2"></i>Permissions must be valid JSON array
                </p>
            </div>

            <div class="dashboard-card mt-3">
                <h5 class="mb-3"><i class="fas fa-list me-2"></i>Common Permissions</h5>
                <div class="permission-list">
                    <p class="small mb-1"><code>users.view</code></p>
                    <p class="small mb-1"><code>users.create</code></p>
                    <p class="small mb-1"><code>users.edit</code></p>
                    <p class="small mb-1"><code>users.delete</code></p>
                    <p class="small mb-1"><code>roles.manage</code></p>
                    <p class="small mb-1"><code>projects.manage</code></p>
                    <p class="small mb-1"><code>clients.manage</code></p>
                    <p class="small mb-1"><code>settings.manage</code></p>
                    <p class="small mb-0"><code>reports.view</code></p>
                </div>
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
    .permission-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .permission-list code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
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
