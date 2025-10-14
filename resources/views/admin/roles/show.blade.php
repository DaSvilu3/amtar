@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Role Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Role
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-user-shield me-2"></i>Role Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Role Name:</strong>
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-primary">{{ $role->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $role->description ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Users Count:</strong>
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-info">{{ $role->users_count ?? 0 }} users</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-shield-alt me-2"></i>Permissions</h5>
                @php
                    $permissions = is_string($role->permissions ?? '') ? json_decode($role->permissions, true) : ($role->permissions ?? []);
                @endphp

                @if(!empty($permissions) && is_array($permissions))
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <code>{{ $permission }}</code>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No permissions assigned</p>
                @endif

                <div class="mt-4">
                    <h6>Raw JSON:</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ is_string($role->permissions ?? '') ? $role->permissions : json_encode($role->permissions ?? [], JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <div class="mb-3">
                    <small class="text-muted">Created At:</small>
                    <p class="mb-0">{{ $role->created_at ? $role->created_at->format('M d, Y H:i:s') : '-' }}</p>
                </div>
                <div>
                    <small class="text-muted">Updated At:</small>
                    <p class="mb-0">{{ $role->updated_at ? $role->updated_at->format('M d, Y H:i:s') : '-' }}</p>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-users me-2"></i>Users with this Role</h5>
                @forelse($role->users ?? [] as $user)
                    <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                        <div class="user-avatar me-2">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No users assigned to this role</p>
                @endforelse
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
    .dashboard-card h5 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
    }
    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--secondary-color);
        color: var(--primary-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
    }
</style>
@endpush
