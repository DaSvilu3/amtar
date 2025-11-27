@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')
<div class="fade-in">
    <!-- Compact Header Section -->
    <div class="project-header-compact">
        <div class="header-left">
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="project-title-compact">{{ $project->name }}</h1>
                <span class="status-badge-compact status-{{ $project->status }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
            <div class="project-meta">
                <span><i class="fas fa-hashtag"></i>{{ $project->project_number }}</span>
                <span><i class="fas fa-user-tie"></i>{{ $project->client->name ?? '-' }}</span>
                <span><i class="fas fa-user-cog"></i>{{ $project->projectManager->name ?? '-' }}</span>
                @if($project->location)
                    <span><i class="fas fa-map-marker-alt"></i>{{ $project->location }}</span>
                @endif
            </div>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Compact Metrics Row -->
    <div class="metrics-row">
        <div class="metric-item">
            <div class="metric-icon-compact bg-primary">
                <i class="fas fa-tasks"></i>
            </div>
            <div>
                <div class="metric-label-compact">Tasks</div>
                <div class="metric-value-compact">{{ $project->tasks->where('status', 'completed')->count() }}/{{ $project->tasks->count() }}</div>
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-icon-compact bg-success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <div class="metric-label-compact">Budget</div>
                <div class="metric-value-compact">{{ $project->budget ? number_format($project->budget, 0) . ' OMR' : '-' }}</div>
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-icon-compact bg-info">
                <i class="fas fa-cogs"></i>
            </div>
            <div>
                <div class="metric-label-compact">Services</div>
                <div class="metric-value-compact">{{ $project->services->count() }}</div>
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-icon-compact bg-warning">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <div class="metric-label-compact">Documents</div>
                <div class="metric-value-compact">{{ $project->files->count() }}</div>
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-icon-compact bg-secondary">
                <i class="fas fa-file-contract"></i>
            </div>
            <div>
                <div class="metric-label-compact">Contracts</div>
                <div class="metric-value-compact">{{ $project->contracts->count() }}</div>
            </div>
        </div>
        <div class="metric-item">
            <div class="metric-icon-compact bg-dark">
                <i class="fas fa-calendar"></i>
            </div>
            <div>
                <div class="metric-label-compact">Duration</div>
                <div class="metric-value-compact">
                    @if($project->start_date && $project->end_date)
                        {{ $project->start_date->diffInDays($project->end_date) }} days
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="tabs-container">
        <ul class="nav nav-tabs custom-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-home me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab">
                    <i class="fas fa-list-check me-2"></i>Services
                    <span class="tab-badge">{{ $project->services->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                    <i class="fas fa-file-alt me-2"></i>Documents
                    <span class="tab-badge">{{ $project->files->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts" type="button" role="tab">
                    <i class="fas fa-file-contract me-2"></i>Contracts
                    <span class="tab-badge">{{ $project->contracts->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                    <i class="fas fa-tasks me-2"></i>Tasks
                    <span class="tab-badge">{{ $project->tasks->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="milestones-tab" data-bs-toggle="tab" data-bs-target="#milestones" type="button" role="tab">
                    <i class="fas fa-flag me-2"></i>Milestones
                    <span class="tab-badge">{{ $project->milestones->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab">
                    <i class="fas fa-clock me-2"></i>Timeline
                </button>
            </li>
        </ul>

        <div class="tab-content custom-tab-content">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row g-3">
                    <!-- Service Hierarchy -->
                    <div class="col-lg-8">
                        <div class="compact-card">
                            <div class="compact-card-header">
                                <i class="fas fa-sitemap me-2"></i>Service Structure
                            </div>
                            <div class="compact-card-body">
                                <div class="hierarchy-compact">
                                    <div class="hierarchy-row">
                                        <span class="hierarchy-label-compact">Main Service:</span>
                                        <span class="hierarchy-value-compact">
                                            <i class="fas fa-layer-group me-2 text-primary"></i>
                                            {{ $project->mainService->name ?? '-' }}
                                        </span>
                                    </div>
                                    @if($project->subService)
                                        <div class="hierarchy-divider"></div>
                                        <div class="hierarchy-row">
                                            <span class="hierarchy-label-compact">Sub Service:</span>
                                            <span class="hierarchy-value-compact">
                                                <i class="fas fa-arrow-right me-2 text-secondary"></i>
                                                {{ $project->subService->name }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($project->servicePackage)
                                        <div class="hierarchy-divider"></div>
                                        <div class="hierarchy-row">
                                            <span class="hierarchy-label-compact">Package:</span>
                                            <span class="hierarchy-value-compact">
                                                <i class="fas fa-box me-2 text-info"></i>
                                                {{ $project->servicePackage->name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details -->
                    <div class="col-lg-4">
                        <div class="compact-card">
                            <div class="compact-card-header">
                                <i class="fas fa-info-circle me-2"></i>Details
                            </div>
                            <div class="compact-card-body">
                                <div class="detail-row">
                                    <span class="detail-label"><i class="fas fa-calendar-start me-1"></i>Start:</span>
                                    <span class="detail-value">{{ $project->start_date ? $project->start_date->format('M d, Y') : '-' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="fas fa-calendar-check me-1"></i>End:</span>
                                    <span class="detail-value">{{ $project->end_date ? $project->end_date->format('M d, Y') : '-' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="fas fa-clock me-1"></i>Created:</span>
                                    <span class="detail-value">{{ $project->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label"><i class="fas fa-sync me-1"></i>Updated:</span>
                                    <span class="detail-value">{{ $project->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($project->description)
                        <div class="col-12">
                            <div class="compact-card">
                                <div class="compact-card-header">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </div>
                                <div class="compact-card-body">
                                    <p class="mb-0">{{ $project->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Services Tab -->
            <div class="tab-pane fade" id="services" role="tabpanel">
                @php
                    $projectServices = $project->services()->with('service.serviceStage')->orderBy('sort_order')->get();
                    $groupedServices = $projectServices->groupBy(function($item) {
                        return $item->service->serviceStage->name ?? 'Uncategorized';
                    });
                @endphp

                @forelse($groupedServices as $stageName => $stageServices)
                    <div class="compact-card mb-3">
                        <div class="compact-card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <span>
                                    <i class="fas fa-flag me-2"></i>{{ $stageName }}
                                </span>
                                <span class="badge bg-light text-dark">{{ $stageServices->count() }} services</span>
                            </div>
                        </div>
                        <div class="compact-card-body p-0">
                            <div class="service-list-compact">
                                @foreach($stageServices as $projectService)
                                    <div class="service-list-item">
                                        <div class="service-checkbox-compact">
                                            @if($projectService->is_completed)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="far fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="service-name-compact">{{ $projectService->service->name }}</div>
                                        <div class="service-badges-compact">
                                            <span class="mini-badge {{ $projectService->is_from_package ? 'badge-package' : 'badge-custom' }}">
                                                {{ $projectService->is_from_package ? 'Package' : 'Custom' }}
                                            </span>
                                            @if($projectService->is_completed)
                                                <span class="mini-badge badge-completed">✓</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="compact-card">
                        <div class="compact-card-body text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No services assigned to this project.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                @if($project->files->count() > 0)
                    <div class="compact-card">
                        <div class="compact-card-body p-0">
                            <div class="document-list-compact">
                                @foreach($project->files as $file)
                                    <div class="document-list-item">
                                        <div class="document-icon-compact">
                                            @php
                                                $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                                $iconClass = match(strtolower($extension)) {
                                                    'pdf' => 'fa-file-pdf text-danger',
                                                    'jpg', 'jpeg', 'png' => 'fa-file-image text-primary',
                                                    'doc', 'docx' => 'fa-file-word text-info',
                                                    'xls', 'xlsx' => 'fa-file-excel text-success',
                                                    default => 'fa-file text-secondary'
                                                };
                                            @endphp
                                            <i class="fas {{ $iconClass }}"></i>
                                        </div>
                                        <div class="document-details-compact">
                                            <div class="document-type-compact">{{ $file->documentType->name ?? $file->name }}</div>
                                            <div class="document-name-compact">{{ $file->original_name }}</div>
                                            <div class="document-meta-compact">
                                                {{ number_format($file->file_size / 1024, 2) }} KB • {{ $file->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn-download-compact">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="compact-card">
                        <div class="compact-card-body text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No documents uploaded yet.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Contracts Tab -->
            <div class="tab-pane fade" id="contracts" role="tabpanel">
                @if($project->contracts->count() > 0)
                    <div class="compact-card">
                        <div class="compact-card-body p-0">
                            <div class="contract-list-compact">
                                @foreach($project->contracts as $contract)
                                    <a href="{{ route('admin.contracts.show', $contract->id) }}" class="contract-list-item">
                                        <div class="contract-icon-compact">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                        <div class="contract-details-compact">
                                            <div class="contract-number-compact">{{ $contract->contract_number }}</div>
                                            <div class="contract-title-compact">{{ $contract->title }}</div>
                                            <div class="contract-meta-compact">
                                                <span class="mini-badge bg-{{ $contract->status === 'active' ? 'success' : ($contract->status === 'draft' ? 'secondary' : 'warning') }}">
                                                    {{ ucfirst($contract->status) }}
                                                </span>
                                                @if($contract->auto_generated)
                                                    <span class="mini-badge bg-info">Auto</span>
                                                @endif
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="compact-card">
                        <div class="compact-card-body text-center text-muted py-5">
                            <i class="fas fa-file-contract fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No contracts associated with this project.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tasks Tab -->
            <div class="tab-pane fade" id="tasks" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @php
                            $completedTasks = $project->tasks->where('status', 'completed')->count();
                            $totalTasks = $project->tasks->count();
                            $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <span class="text-muted">{{ $completedTasks }}/{{ $totalTasks }} completed ({{ $taskProgress }}%)</span>
                    </div>
                    <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Task
                    </a>
                </div>

                @if($project->tasks->count() > 0)
                    <div class="compact-card">
                        <div class="compact-card-body p-0">
                            <div class="task-list-compact">
                                @foreach($project->tasks->sortBy('sort_order') as $task)
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="task-list-item">
                                        <div class="task-checkbox-compact">
                                            @if($task->status === 'completed')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif($task->status === 'in_progress')
                                                <i class="fas fa-spinner text-primary"></i>
                                            @elseif($task->status === 'review')
                                                <i class="fas fa-eye text-warning"></i>
                                            @else
                                                <i class="far fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="task-details-compact">
                                            <div class="task-title-compact">{{ $task->title }}</div>
                                            <div class="task-meta-compact">
                                                @if($task->projectService)
                                                    <span><i class="fas fa-cog"></i> {{ $task->projectService->service->name ?? '' }}</span>
                                                @endif
                                                @if($task->assignedTo)
                                                    <span><i class="fas fa-user"></i> {{ $task->assignedTo->name }}</span>
                                                @endif
                                                @if($task->due_date)
                                                    <span class="{{ $task->isOverdue() ? 'text-danger' : '' }}">
                                                        <i class="fas fa-clock"></i> {{ $task->due_date->format('M d') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="task-badges-compact">
                                            <span class="mini-badge badge-priority-{{ $task->priority }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                            <span class="mini-badge badge-status-{{ $task->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </div>
                                        <i class="fas fa-chevron-right text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="compact-card">
                        <div class="compact-card-body text-center text-muted py-5">
                            <i class="fas fa-tasks fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No tasks for this project yet.</p>
                            <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-outline-primary btn-sm mt-3">
                                <i class="fas fa-plus me-1"></i>Create First Task
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Milestones Tab -->
            <div class="tab-pane fade" id="milestones" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @php
                            $completedMilestones = $project->milestones->where('status', 'completed')->count();
                            $totalMilestones = $project->milestones->count();
                        @endphp
                        <span class="text-muted">{{ $completedMilestones }}/{{ $totalMilestones }} completed</span>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.milestones.generate', $project) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Auto-generate from service stages">
                                <i class="fas fa-magic me-1"></i>Auto Generate
                            </button>
                        </form>
                        <a href="{{ route('admin.milestones.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Milestone
                        </a>
                    </div>
                </div>

                @if($project->milestones->count() > 0)
                    <div class="row g-3">
                        @foreach($project->milestones->sortBy('sort_order') as $milestone)
                            <div class="col-md-6">
                                <div class="compact-card milestone-card">
                                    <div class="compact-card-header d-flex justify-content-between align-items-center">
                                        <span>
                                            @if($milestone->status === 'completed')
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @elseif($milestone->status === 'in_progress')
                                                <i class="fas fa-spinner text-primary me-2"></i>
                                            @elseif($milestone->status === 'overdue')
                                                <i class="fas fa-exclamation-circle text-danger me-2"></i>
                                            @else
                                                <i class="fas fa-flag text-secondary me-2"></i>
                                            @endif
                                            {{ $milestone->title }}
                                        </span>
                                        <span class="mini-badge bg-{{ $milestone->status === 'completed' ? 'success' : ($milestone->status === 'in_progress' ? 'primary' : ($milestone->status === 'overdue' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                        </span>
                                    </div>
                                    <div class="compact-card-body">
                                        @if($milestone->serviceStage)
                                            <div class="mb-2">
                                                <span class="mini-badge bg-light text-dark">
                                                    <i class="fas fa-layer-group me-1"></i>{{ $milestone->serviceStage->name }}
                                                </span>
                                            </div>
                                        @endif

                                        @php $progress = $milestone->calculateProgress(); @endphp
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $milestone->status === 'completed' ? 'success' : 'primary' }}"
                                                 style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>{{ $milestone->tasks->count() }} tasks</span>
                                            <span>{{ $progress }}% complete</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="small text-muted">
                                                @if($milestone->target_date)
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <span class="{{ $milestone->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                                        {{ $milestone->target_date->format('M d, Y') }}
                                                    </span>
                                                @endif
                                                @if($milestone->payment_percentage)
                                                    <span class="ms-2"><i class="fas fa-percent me-1"></i>{{ $milestone->payment_percentage }}%</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('admin.milestones.show', $milestone) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="compact-card">
                        <div class="compact-card-body text-center text-muted py-5">
                            <i class="fas fa-flag fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No milestones for this project yet.</p>
                            <div class="mt-3">
                                <form action="{{ route('admin.milestones.generate', $project) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-magic me-1"></i>Auto Generate from Stages
                                    </button>
                                </form>
                                <span class="text-muted mx-2">or</span>
                                <a href="{{ route('admin.milestones.create', ['project_id' => $project->id]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Create Manually
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Timeline Tab -->
            <div class="tab-pane fade" id="timeline" role="tabpanel">
                <div class="compact-card">
                    <div class="compact-card-body">
                        <div class="timeline-compact">
                            <div class="timeline-item-compact">
                                <div class="timeline-dot-compact bg-primary"></div>
                                <div class="timeline-content-compact">
                                    <div class="timeline-title-compact">Project Created</div>
                                    <div class="timeline-time-compact">{{ $project->created_at->format('M d, Y H:i') }}</div>
                                    <div class="timeline-ago-compact">{{ $project->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="timeline-item-compact">
                                <div class="timeline-dot-compact bg-info"></div>
                                <div class="timeline-content-compact">
                                    <div class="timeline-title-compact">Last Updated</div>
                                    <div class="timeline-time-compact">{{ $project->updated_at->format('M d, Y H:i') }}</div>
                                    <div class="timeline-ago-compact">{{ $project->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @if($project->start_date)
                                <div class="timeline-item-compact">
                                    <div class="timeline-dot-compact bg-success"></div>
                                    <div class="timeline-content-compact">
                                        <div class="timeline-title-compact">Project Start Date</div>
                                        <div class="timeline-time-compact">{{ $project->start_date->format('M d, Y') }}</div>
                                        @if($project->start_date->isFuture())
                                            <div class="timeline-ago-compact">Starts {{ $project->start_date->diffForHumans() }}</div>
                                        @else
                                            <div class="timeline-ago-compact">Started {{ $project->start_date->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($project->end_date)
                                <div class="timeline-item-compact">
                                    <div class="timeline-dot-compact bg-warning"></div>
                                    <div class="timeline-content-compact">
                                        <div class="timeline-title-compact">Project End Date</div>
                                        <div class="timeline-time-compact">{{ $project->end_date->format('M d, Y') }}</div>
                                        @if($project->end_date->isFuture())
                                            <div class="timeline-ago-compact">Ends {{ $project->end_date->diffForHumans() }}</div>
                                        @else
                                            <div class="timeline-ago-compact">Ended {{ $project->end_date->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Compact Header */
    .project-header-compact {
        background: white;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .project-title-compact {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .status-badge-compact {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-planning { background: #e3f2fd; color: #1976d2; }
    .status-in_progress { background: #e8f5e9; color: #388e3c; }
    .status-on_hold { background: #fff3e0; color: #f57c00; }
    .status-completed { background: #f3e5f5; color: #7b1fa2; }
    .status-cancelled { background: #ffebee; color: #c62828; }

    .project-meta {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #6c757d;
        margin-top: 8px;
    }

    .project-meta span i {
        margin-right: 4px;
        color: var(--secondary-color);
    }

    .header-right {
        display: flex;
        gap: 8px;
    }

    /* Compact Metrics Row */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 15px;
    }

    .metric-item {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .metric-icon-compact {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    .metric-label-compact {
        font-size: 11px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
    }

    .metric-value-compact {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Tabs Container */
    .tabs-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .custom-tabs {
        border-bottom: 2px solid #e9ecef;
        padding: 0 20px;
        background: #f8f9fa;
    }

    .custom-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 15px 20px;
        position: relative;
        transition: all 0.3s ease;
        border-radius: 0;
    }

    .custom-tabs .nav-link:hover {
        color: var(--primary-color);
        background: rgba(243, 200, 135, 0.1);
    }

    .custom-tabs .nav-link.active {
        color: var(--primary-color);
        background: white;
        border-bottom: 3px solid var(--secondary-color);
    }

    .tab-badge {
        background: var(--secondary-color);
        color: var(--primary-color);
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        margin-left: 5px;
    }

    .custom-tab-content {
        padding: 20px;
        min-height: 400px;
    }

    /* Compact Cards */
    .compact-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }

    .compact-card-header {
        background: linear-gradient(135deg, var(--primary-color), #4a1a1f);
        color: white;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid var(--secondary-color);
    }

    .compact-card-body {
        padding: 18px;
    }

    /* Hierarchy Compact */
    .hierarchy-compact {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .hierarchy-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hierarchy-label-compact {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        min-width: 100px;
    }

    .hierarchy-value-compact {
        font-weight: 500;
        color: var(--primary-color);
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        flex: 1;
    }

    .hierarchy-divider {
        width: 2px;
        height: 12px;
        background: linear-gradient(to bottom, var(--secondary-color), transparent);
        margin-left: 50px;
    }

    /* Detail Row */
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #6c757d;
        font-weight: 500;
    }

    .detail-value {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Service List Compact */
    .service-list-compact {
        display: flex;
        flex-direction: column;
    }

    .service-list-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s ease;
    }

    .service-list-item:hover {
        background: #f8f9fa;
    }

    .service-list-item:last-child {
        border-bottom: none;
    }

    .service-checkbox-compact {
        font-size: 16px;
    }

    .service-name-compact {
        flex: 1;
        font-weight: 500;
        color: var(--primary-color);
        font-size: 14px;
    }

    .service-badges-compact {
        display: flex;
        gap: 6px;
    }

    .mini-badge {
        font-size: 9px;
        padding: 3px 7px;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-package { background: #e3f2fd; color: #1976d2; }
    .badge-custom { background: #fff3e0; color: #f57c00; }
    .badge-completed { background: #e8f5e9; color: #388e3c; }

    /* Document List Compact */
    .document-list-compact {
        display: flex;
        flex-direction: column;
    }

    .document-list-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 18px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s ease;
    }

    .document-list-item:hover {
        background: #f8f9fa;
    }

    .document-list-item:last-child {
        border-bottom: none;
    }

    .document-icon-compact {
        font-size: 28px;
        width: 40px;
        text-align: center;
    }

    .document-details-compact {
        flex: 1;
    }

    .document-type-compact {
        font-size: 10px;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .document-name-compact {
        font-weight: 500;
        color: var(--primary-color);
        font-size: 14px;
        margin-bottom: 2px;
    }

    .document-meta-compact {
        font-size: 11px;
        color: #6c757d;
    }

    .btn-download-compact {
        background: var(--secondary-color);
        border: none;
        color: var(--primary-color);
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-download-compact:hover {
        background: #e5b976;
        transform: scale(1.05);
    }

    /* Contract List Compact */
    .contract-list-compact {
        display: flex;
        flex-direction: column;
    }

    .contract-list-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 18px;
        border-bottom: 1px solid #f0f0f0;
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .contract-list-item:hover {
        background: #f8f9fa;
    }

    .contract-list-item:last-child {
        border-bottom: none;
    }

    .contract-icon-compact {
        width: 40px;
        height: 40px;
        background: var(--secondary-color);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: var(--primary-color);
    }

    .contract-details-compact {
        flex: 1;
    }

    .contract-number-compact {
        font-size: 11px;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .contract-title-compact {
        font-weight: 500;
        color: var(--primary-color);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .contract-meta-compact {
        display: flex;
        gap: 6px;
    }

    /* Task List Compact */
    .task-list-compact {
        display: flex;
        flex-direction: column;
    }

    .task-list-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 18px;
        border-bottom: 1px solid #f0f0f0;
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .task-list-item:hover {
        background: #f8f9fa;
    }

    .task-list-item:last-child {
        border-bottom: none;
    }

    .task-checkbox-compact {
        font-size: 18px;
        width: 24px;
        text-align: center;
    }

    .task-details-compact {
        flex: 1;
    }

    .task-title-compact {
        font-weight: 500;
        color: var(--primary-color);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .task-meta-compact {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: #6c757d;
    }

    .task-meta-compact i {
        margin-right: 3px;
    }

    .task-badges-compact {
        display: flex;
        gap: 6px;
    }

    /* Priority badges */
    .badge-priority-low { background: #e8f5e9; color: #388e3c; }
    .badge-priority-medium { background: #fff3e0; color: #f57c00; }
    .badge-priority-high { background: #ffebee; color: #c62828; }
    .badge-priority-urgent { background: #c62828; color: white; }

    /* Status badges */
    .badge-status-pending { background: #e0e0e0; color: #616161; }
    .badge-status-in_progress { background: #e3f2fd; color: #1976d2; }
    .badge-status-review { background: #fff8e1; color: #f57c00; }
    .badge-status-completed { background: #e8f5e9; color: #388e3c; }
    .badge-status-cancelled { background: #ffebee; color: #c62828; }

    /* Milestone Card */
    .milestone-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .milestone-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Timeline Compact */
    .timeline-compact {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 10px 0;
    }

    .timeline-item-compact {
        display: flex;
        gap: 15px;
        position: relative;
    }

    .timeline-item-compact:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 13px;
        top: 35px;
        bottom: -20px;
        width: 2px;
        background: linear-gradient(to bottom, var(--secondary-color), transparent);
    }

    .timeline-dot-compact {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 0 0 2px currentColor;
    }

    .timeline-content-compact {
        flex: 1;
        padding-top: 2px;
    }

    .timeline-title-compact {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 14px;
        margin-bottom: 3px;
    }

    .timeline-time-compact {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .timeline-ago-compact {
        font-size: 11px;
        color: #adb5bd;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .project-header-compact {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .metrics-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .custom-tabs {
            overflow-x: auto;
            white-space: nowrap;
        }

        .custom-tabs .nav-link {
            padding: 12px 15px;
            font-size: 13px;
        }
    }
</style>
@endpush
