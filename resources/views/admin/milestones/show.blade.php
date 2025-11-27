@extends('layouts.admin')

@section('title', 'Milestone Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Milestone Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.milestones.index') }}">Milestones</a></li>
                    <li class="breadcrumb-item active">{{ $milestone->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.milestones.edit', $milestone) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.milestones.index', ['project_id' => $milestone->project_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 style="color: var(--primary-color);">{{ $milestone->title }}</h4>
                        @if($milestone->project)
                            <p class="text-muted mb-0">
                                <i class="fas fa-project-diagram me-1"></i>
                                <a href="{{ route('admin.projects.show', $milestone->project) }}">{{ $milestone->project->name }}</a>
                            </p>
                        @endif
                    </div>
                    <span class="badge bg-{{ $milestone->status === 'completed' ? 'success' : ($milestone->status === 'in_progress' ? 'primary' : ($milestone->status === 'overdue' ? 'danger' : 'secondary')) }}" style="font-size: 14px;">
                        {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                    </span>
                </div>

                @if($milestone->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p>{{ $milestone->description }}</p>
                    </div>
                @endif

                @if($milestone->serviceStage)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Service Stage</h6>
                        <span class="badge bg-light text-dark" style="font-size: 14px;">
                            <i class="fas fa-layer-group me-1"></i>
                            {{ $milestone->serviceStage->name }}
                        </span>
                    </div>
                @endif

                <!-- Progress -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Progress (based on tasks)</h6>
                    @php $progress = $milestone->calculateProgress(); @endphp
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $milestone->status === 'completed' ? 'success' : 'primary' }}"
                             role="progressbar"
                             style="width: {{ $progress }}%;"
                             aria-valuenow="{{ $progress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $progress }}%
                        </div>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0">Tasks ({{ $milestone->tasks->count() }})</h6>
                        <a href="{{ route('admin.tasks.create', ['project_id' => $milestone->project_id, 'milestone_id' => $milestone->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Task
                        </a>
                    </div>

                    @if($milestone->tasks->count() > 0)
                        <div class="list-group">
                            @foreach($milestone->tasks as $task)
                                <a href="{{ route('admin.tasks.show', $task) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($task->status === 'completed')
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @elseif($task->status === 'in_progress')
                                                <i class="fas fa-spinner text-primary me-2"></i>
                                            @else
                                                <i class="fas fa-circle text-secondary me-2"></i>
                                            @endif
                                            <strong>{{ $task->title }}</strong>
                                            @if($task->assignedTo)
                                                <small class="text-muted ms-2">- {{ $task->assignedTo->name }}</small>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'secondary') }} me-2">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                            <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="fas fa-tasks fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No tasks for this milestone yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                    Milestone Information
                </h5>

                <div class="mb-3">
                    <label class="text-muted small">Target Date</label>
                    @if($milestone->target_date)
                        <p class="mb-0 {{ $milestone->isOverdue() ? 'text-danger fw-bold' : '' }}">
                            {{ $milestone->target_date->format('M d, Y') }}
                            @if($milestone->isOverdue())
                                <i class="fas fa-exclamation-triangle"></i>
                            @endif
                        </p>
                    @else
                        <p class="text-muted mb-0">Not set</p>
                    @endif
                </div>

                @if($milestone->completed_at)
                    <div class="mb-3">
                        <label class="text-muted small">Completed At</label>
                        <p class="mb-0 text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $milestone->completed_at->format('M d, Y') }}
                        </p>
                    </div>
                @endif

                <hr>

                <h6 class="text-muted mb-3">Payment Information</h6>

                @if($milestone->payment_percentage)
                    <div class="mb-3">
                        <label class="text-muted small">Payment Percentage</label>
                        <p class="mb-0 fw-bold">{{ $milestone->payment_percentage }}%</p>
                    </div>
                @endif

                @if($milestone->payment_amount)
                    <div class="mb-3">
                        <label class="text-muted small">Payment Amount</label>
                        <p class="mb-0 fw-bold">{{ number_format($milestone->payment_amount, 2) }} OMR</p>
                    </div>
                @endif

                @if(!$milestone->payment_percentage && !$milestone->payment_amount)
                    <p class="text-muted">No payment information set</p>
                @endif

                <hr>

                <div class="mb-3">
                    <label class="text-muted small">Created At</label>
                    <p class="mb-0">{{ $milestone->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div class="mb-0">
                    <label class="text-muted small">Last Updated</label>
                    <p class="mb-0">{{ $milestone->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
</style>
@endpush
