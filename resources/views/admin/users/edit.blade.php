@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-title mb-4">
        <h1>Edit User</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name ?? '') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email ?? '') }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password"
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   id="password_confirmation"
                                   name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="roles" class="form-label">Roles <span class="text-danger">*</span></label>
                        <select class="form-select @error('roles') is-invalid @enderror"
                                id="roles"
                                name="roles[]"
                                multiple
                                size="5">
                            @foreach($roles ?? [] as $role)
                                <option value="{{ $role->id }}"
                                    {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('roles')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple roles</small>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text"
                               class="form-control @error('phone') is-invalid @enderror"
                               id="phone"
                               name="phone"
                               value="{{ old('phone', $user->phone ?? '') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Leave password blank to keep current
                </p>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Email must be unique
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-check text-success me-2"></i>Users can have multiple roles
                </p>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $user->created_at ? $user->created_at->format('M d, Y H:i') : '-' }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $user->updated_at ? $user->updated_at->format('M d, Y H:i') : '-' }}
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
