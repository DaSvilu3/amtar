@extends('layouts.admin')

@section('title', 'Activity Details - Amtar Admin')

@section('content')
<div class="page-title">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Activity Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.activity-logs.index') }}">Activity Log</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Details -->
        <div class="dashboard-card mb-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar-lg {{ $activityLog->action_badge_class }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="fas {{ $activityLog->action_icon }} fa-lg"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ ucfirst($activityLog->action) }} {{ $activityLog->model_label }}</h4>
                    <span class="text-muted">{{ $activityLog->created_at->format('F d, Y \a\t h:i:s A') }}</span>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label text-muted small">User</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            {{ strtoupper(substr($activityLog->user_name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-medium">{{ $activityLog->user_name ?? 'System' }}</div>
                            @if($activityLog->user)
                                <small class="text-muted">{{ $activityLog->user->email }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Subject</label>
                    <div>
                        <span class="badge bg-light text-dark me-2">{{ $activityLog->model_label }}</span>
                        <span class="fw-medium">{{ $activityLog->model_name ?? 'N/A' }}</span>
                        @if($activityLog->model_id)
                            <small class="text-muted">(ID: {{ $activityLog->model_id }})</small>
                        @endif
                    </div>
                </div>
            </div>

            @if($activityLog->description)
                <div class="mt-4">
                    <label class="form-label text-muted small">Description</label>
                    <p class="mb-0">{{ $activityLog->description }}</p>
                </div>
            @endif
        </div>

        <!-- Changes -->
        @if($activityLog->old_values || $activityLog->new_values)
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-exchange-alt me-2 text-muted"></i>Changes</h5>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Field</th>
                                <th style="width: 37.5%;">Old Value</th>
                                <th style="width: 37.5%;">New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allKeys = array_unique(array_merge(
                                    array_keys($activityLog->old_values ?? []),
                                    array_keys($activityLog->new_values ?? [])
                                ));
                            @endphp
                            @foreach($allKeys as $key)
                                @php
                                    $oldValue = $activityLog->old_values[$key] ?? null;
                                    $newValue = $activityLog->new_values[$key] ?? null;
                                @endphp
                                <tr>
                                    <td class="fw-medium">{{ Str::title(str_replace('_', ' ', $key)) }}</td>
                                    <td>
                                        @if($activityLog->action === 'created')
                                            <span class="text-muted">-</span>
                                        @elseif(is_array($oldValue) || is_object($oldValue))
                                            <code class="text-danger small">{{ json_encode($oldValue) }}</code>
                                        @elseif(is_null($oldValue))
                                            <span class="text-muted">null</span>
                                        @elseif($oldValue === '')
                                            <span class="text-muted">(empty)</span>
                                        @else
                                            <span class="text-danger">{{ Str::limit((string) $oldValue, 100) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activityLog->action === 'deleted')
                                            <span class="text-muted">-</span>
                                        @elseif(is_array($newValue) || is_object($newValue))
                                            <code class="text-success small">{{ json_encode($newValue) }}</code>
                                        @elseif(is_null($newValue))
                                            <span class="text-muted">null</span>
                                        @elseif($newValue === '')
                                            <span class="text-muted">(empty)</span>
                                        @else
                                            <span class="text-success">{{ Str::limit((string) $newValue, 100) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Technical Details -->
        <div class="dashboard-card">
            <h5 class="mb-4"><i class="fas fa-info-circle me-2 text-muted"></i>Technical Details</h5>

            <div class="mb-3">
                <label class="form-label text-muted small">Log ID</label>
                <div class="fw-medium">#{{ $activityLog->id }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Timestamp</label>
                <div class="fw-medium">{{ $activityLog->created_at->toIso8601String() }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">IP Address</label>
                <div class="fw-medium font-monospace">{{ $activityLog->ip_address ?? 'N/A' }}</div>
            </div>

            @if($activityLog->user_agent)
                <div class="mb-3">
                    <label class="form-label text-muted small">User Agent</label>
                    <div class="small text-muted" style="word-break: break-all;">
                        {{ $activityLog->user_agent }}
                    </div>
                </div>
            @endif

            @if($activityLog->model_type)
                <div class="mb-0">
                    <label class="form-label text-muted small">Model Class</label>
                    <div class="font-monospace small">{{ $activityLog->model_type }}</div>
                </div>
            @endif
        </div>

        <!-- Time Info -->
        <div class="dashboard-card mt-4">
            <h5 class="mb-4"><i class="fas fa-clock me-2 text-muted"></i>Time Information</h5>

            <div class="mb-3">
                <label class="form-label text-muted small">Relative Time</label>
                <div class="fw-medium">{{ $activityLog->created_at->diffForHumans() }}</div>
            </div>

            <div class="mb-0">
                <label class="form-label text-muted small">Local Time</label>
                <div class="fw-medium">{{ $activityLog->created_at->format('l, F d, Y') }}</div>
                <div class="text-muted">{{ $activityLog->created_at->format('h:i:s A T') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
