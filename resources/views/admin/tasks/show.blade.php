@extends('layouts.admin')

@section('title', 'Task Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Task Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
                    <li class="breadcrumb-item active">{{ $task->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.tasks.index', ['project_id' => $task->project_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 style="color: var(--primary-color);">{{ $task->title }}</h4>
                        @if($task->project)
                            <p class="text-muted mb-0">
                                <i class="fas fa-project-diagram me-1"></i>
                                <a href="{{ route('admin.projects.show', $task->project) }}">{{ $task->project->name }}</a>
                            </p>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }} mb-2" style="font-size: 14px;">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                        <br>
                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : ($task->status === 'review' ? 'warning' : 'secondary')) }}" style="font-size: 14px;">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>

                @if($task->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p>{{ $task->description }}</p>
                    </div>
                @endif

                @if($task->projectService)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Linked Service</h6>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-cogs me-2" style="color: var(--secondary-color);"></i>
                            <span>{{ $task->projectService->service->name ?? 'Unknown Service' }}</span>
                            @if($task->projectService->serviceStage)
                                <span class="badge bg-light text-dark ms-2">{{ $task->projectService->serviceStage->name }}</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if($task->milestone)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Milestone</h6>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-flag me-2" style="color: var(--secondary-color);"></i>
                            <a href="{{ route('admin.milestones.show', $task->milestone) }}">{{ $task->milestone->title }}</a>
                        </div>
                    </div>
                @endif

                <!-- Progress Bar -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Progress</h6>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-{{ $task->status === 'completed' ? 'success' : 'primary' }}"
                             role="progressbar"
                             style="width: {{ $task->progress }}%;"
                             aria-valuenow="{{ $task->progress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ $task->progress }}%
                        </div>
                    </div>
                </div>

                <!-- Dependencies -->
                @if($task->dependencies->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Dependencies (Must be completed first)</h6>
                        <div class="list-group">
                            @foreach($task->dependencies as $dep)
                                <a href="{{ route('admin.tasks.show', $dep) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>
                                        @if($dep->status === 'completed')
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                        @else
                                            <i class="fas fa-clock text-warning me-2"></i>
                                        @endif
                                        {{ $dep->title }}
                                    </span>
                                    <span class="badge bg-{{ $dep->status === 'completed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $dep->status)) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                        @if($task->isBlocked())
                            <div class="alert alert-warning mt-2 mb-0">
                                <i class="fas fa-lock me-2"></i>
                                This task is blocked until all dependencies are completed.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Dependents -->
                @if($task->dependents->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Blocking Tasks (Waiting for this task)</h6>
                        <div class="list-group">
                            @foreach($task->dependents as $dep)
                                <a href="{{ route('admin.tasks.show', $dep) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>{{ $dep->title }}</span>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $dep->status)) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                    Task Information
                </h5>

                <div class="mb-3">
                    <label class="text-muted small">Assigned To</label>
                    @if($task->assignedTo)
                        <div class="d-flex align-items-center">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; margin-right: 10px;">
                                {{ strtoupper(substr($task->assignedTo->name, 0, 2)) }}
                            </div>
                            <span>{{ $task->assignedTo->name }}</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">Unassigned</p>
                    @endif
                </div>

                <hr>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="text-muted small">Start Date</label>
                        <p class="mb-0">{{ $task->start_date?->format('M d, Y') ?? '-' }}</p>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="text-muted small">Due Date</label>
                        <p class="mb-0 {{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">
                            {{ $task->due_date?->format('M d, Y') ?? '-' }}
                            @if($task->isOverdue())
                                <i class="fas fa-exclamation-triangle"></i>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="text-muted small">Estimated Hours</label>
                        <p class="mb-0">{{ $task->estimated_hours ?? '-' }}</p>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="text-muted small">Actual Hours</label>
                        <p class="mb-0">{{ $task->actual_hours ?? '-' }}</p>
                    </div>
                </div>

                @if($task->completed_at)
                    <div class="mb-3">
                        <label class="text-muted small">Completed At</label>
                        <p class="mb-0 text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $task->completed_at->format('M d, Y') }}
                        </p>
                    </div>
                @endif

                <hr>

                <div class="mb-3">
                    <label class="text-muted small">Created By</label>
                    <p class="mb-0">{{ $task->createdBy->name ?? 'System' }}</p>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Created At</label>
                    <p class="mb-0">{{ $task->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div class="mb-0">
                    <label class="text-muted small">Last Updated</label>
                    <p class="mb-0">{{ $task->updated_at->format('M d, Y h:i A') }}</p>
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
