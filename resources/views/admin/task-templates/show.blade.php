@extends('layouts.admin')

@section('title', 'Task Template Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Task Template Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.task-templates.index') }}">Task Templates</a></li>
                    <li class="breadcrumb-item active">{{ $taskTemplate->title }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.task-templates.edit', $taskTemplate) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.task-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 style="color: var(--primary-color);">{{ $taskTemplate->title }}</h4>
                        <div class="mt-2">
                            <span class="badge bg-{{ $taskTemplate->priority === 'urgent' ? 'danger' : ($taskTemplate->priority === 'high' ? 'warning' : ($taskTemplate->priority === 'medium' ? 'info' : 'secondary')) }} me-2">
                                {{ ucfirst($taskTemplate->priority) }} Priority
                            </span>
                            <span class="badge bg-{{ $taskTemplate->is_active ? 'success' : 'secondary' }}">
                                {{ $taskTemplate->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($taskTemplate->requires_review)
                                <span class="badge bg-warning ms-2">
                                    <i class="fas fa-eye me-1"></i>Review Required
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($taskTemplate->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p>{{ $taskTemplate->description }}</p>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Service</h6>
                        <p><strong>{{ $taskTemplate->service->name ?? '-' }}</strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Service Stage</h6>
                        <p>
                            @if($taskTemplate->serviceStage)
                                <span class="badge bg-info">{{ $taskTemplate->serviceStage->name }}</span>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($requiredSkills->count() > 0)
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-cogs me-2" style="color: var(--secondary-color);"></i>
                    Required Skills
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($requiredSkills as $skill)
                        <span class="badge bg-{{ $skill->category === 'technical' ? 'primary' : ($skill->category === 'domain' ? 'info' : ($skill->category === 'certification' ? 'success' : 'secondary')) }}" style="font-size: 0.9em;">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($taskTemplate->dependencies->count() > 0)
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-project-diagram me-2" style="color: var(--secondary-color);"></i>
                    Dependencies (Must Complete First)
                </h5>
                <div class="list-group">
                    @foreach($taskTemplate->dependencies as $dep)
                        <a href="{{ route('admin.task-templates.show', $dep) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ $dep->title }}
                            <span class="badge bg-{{ $dep->priority === 'urgent' ? 'danger' : ($dep->priority === 'high' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($dep->priority) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($taskTemplate->dependents->count() > 0)
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-sitemap me-2" style="color: var(--secondary-color);"></i>
                    Dependent Templates (Blocked by This)
                </h5>
                <div class="list-group">
                    @foreach($taskTemplate->dependents as $dep)
                        <a href="{{ route('admin.task-templates.show', $dep) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ $dep->title }}
                            <span class="badge bg-{{ $dep->priority === 'urgent' ? 'danger' : ($dep->priority === 'high' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($dep->priority) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-clock me-2" style="color: var(--secondary-color);"></i>
                    Time & Effort
                </h5>

                <div class="mb-3">
                    <small class="text-muted">Estimated Hours</small>
                    <h5>{{ $taskTemplate->estimated_hours ?? '-' }} hours</h5>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Default Duration</small>
                    <h5>{{ $taskTemplate->default_duration_days ?? '-' }} days</h5>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-user-tie me-2" style="color: var(--secondary-color);"></i>
                    Requirements
                </h5>

                <div class="mb-3">
                    <small class="text-muted">Minimum Expertise Level</small>
                    <h5>
                        @if($taskTemplate->required_expertise_level)
                            <span class="badge bg-{{ $taskTemplate->required_expertise_level === 'lead' ? 'danger' : ($taskTemplate->required_expertise_level === 'senior' ? 'warning' : 'info') }}">
                                {{ ucfirst($taskTemplate->required_expertise_level) }}
                            </span>
                        @else
                            <span class="text-muted">No minimum</span>
                        @endif
                    </h5>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Review Required</small>
                    <h5>
                        @if($taskTemplate->requires_review)
                            <span class="badge bg-warning"><i class="fas fa-check"></i> Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </h5>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-info me-2" style="color: var(--secondary-color);"></i>
                    Metadata
                </h5>

                <div class="mb-2">
                    <small class="text-muted">Sort Order</small>
                    <p class="mb-0">{{ $taskTemplate->sort_order }}</p>
                </div>

                <div class="mb-2">
                    <small class="text-muted">Created</small>
                    <p class="mb-0">{{ $taskTemplate->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="mb-0">
                    <small class="text-muted">Last Updated</small>
                    <p class="mb-0">{{ $taskTemplate->updated_at->format('M d, Y H:i') }}</p>
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
