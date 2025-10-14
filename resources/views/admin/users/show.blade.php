@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>User Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-user me-2"></i>User Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Full Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->name ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-md-9">
                        <a href="mailto:{{ $user->email ?? '' }}">{{ $user->email ?? '-' }}</a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Phone:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->phone ?? '-' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-md-9">
                        @if(($user->status ?? 'active') == 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Roles:</strong>
                    </div>
                    <div class="col-md-9">
                        @forelse($user->roles ?? [] as $role)
                            <span class="badge bg-info me-1">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-shield-alt me-2"></i>Permissions</h5>
                <div class="row">
                    @php
                        $permissions = [];
                        foreach($user->roles ?? [] as $role) {
                            foreach($role->permissions ?? [] as $permission) {
                                $permissions[] = $permission;
                            }
                        }
                        $permissions = array_unique($permissions);
                    @endphp

                    @forelse($permissions as $permission)
                        <div class="col-md-6 mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $permission }}
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">No permissions assigned</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <div class="mb-3">
                    <small class="text-muted">Created At:</small>
                    <p class="mb-0">{{ $user->created_at ? $user->created_at->format('M d, Y H:i:s') : '-' }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Updated At:</small>
                    <p class="mb-0">{{ $user->updated_at ? $user->updated_at->format('M d, Y H:i:s') : '-' }}</p>
                </div>
                <div>
                    <small class="text-muted">Last Login:</small>
                    <p class="mb-0">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i:s') : 'Never' }}</p>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Activity Stats</h5>
                <div class="mb-3">
                    <small class="text-muted">Total Logins:</small>
                    <p class="mb-0 h4">{{ $user->login_count ?? 0 }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Projects Managed:</small>
                    <p class="mb-0 h4">{{ $user->projects_count ?? 0 }}</p>
                </div>
                <div>
                    <small class="text-muted">Tasks Assigned:</small>
                    <p class="mb-0 h4">{{ $user->tasks_count ?? 0 }}</p>
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
    .dashboard-card h5 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
    }
</style>
@endpush
