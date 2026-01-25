@extends('layouts.admin')

@section('title', 'Activity Log - Amtar Admin')

@section('content')
<div class="page-title">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Activity Log</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Log</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="fas fa-trash me-2"></i>Clear Old Logs
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="dashboard-card mb-4">
    <form action="{{ route('admin.activity-logs.index') }}" method="GET">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label small text-muted">User</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Action</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Model Type</label>
                <select name="model_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search activities..." value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Activity List -->
<div class="dashboard-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 180px;">Date & Time</th>
                    <th style="width: 150px;">User</th>
                    <th style="width: 100px;">Action</th>
                    <th>Details</th>
                    <th style="width: 120px;">IP Address</th>
                    <th style="width: 80px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    <tr>
                        <td>
                            <div class="text-dark">{{ $activity->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $activity->created_at->format('h:i:s A') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ strtoupper(substr($activity->user_name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-truncate" style="max-width: 100px;">{{ $activity->user_name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $activity->action_badge_class }}">
                                <i class="fas {{ $activity->action_icon }} me-1"></i>
                                {{ ucfirst($activity->action) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark">{{ $activity->model_label }}</span>
                                @if($activity->model_name)
                                    <span class="text-truncate" style="max-width: 250px;" title="{{ $activity->model_name }}">
                                        {{ $activity->model_name }}
                                    </span>
                                @endif
                            </div>
                            @if($activity->description)
                                <small class="text-muted d-block">{{ Str::limit($activity->description, 80) }}</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $activity->ip_address }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.activity-logs.show', $activity) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-history fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No activity logs found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $activities->links() }}
        </div>
    @endif
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.activity-logs.clear') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Clear Old Activity Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Delete activity logs older than:</p>
                    <div class="input-group">
                        <input type="number" name="days" class="form-control" value="30" min="1" max="365">
                        <span class="input-group-text">days</span>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        This action cannot be undone.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Old Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
