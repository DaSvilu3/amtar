@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="project-header">
        <div class="header-left">
            <div class="d-flex align-items-center gap-3 mb-2">
                <h1 class="project-title">{{ $project->name }}</h1>
                <span class="status-badge status-{{ $project->status }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
            <div class="project-meta">
                <span><i class="fas fa-hashtag"></i>{{ $project->project_number }}</span>
                <span><i class="fas fa-user-tie"></i>{{ $project->client->name ?? 'No client' }}</span>
                <span><i class="fas fa-user-cog"></i>{{ $project->projectManager->name ?? 'Unassigned' }}</span>
                @if($project->location)
                    <span><i class="fas fa-map-marker-alt"></i>{{ $project->location }}</span>
                @endif
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-row">
        @php
            $completedTasks = $project->tasks->where('status', 'completed')->count();
            $totalTasks = $project->tasks->count();
            $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $completedMilestones = $project->milestones->where('status', 'completed')->count();
            $totalMilestones = $project->milestones->count();
        @endphp
        <div class="stat-item">
            <div class="stat-icon bg-primary"><i class="fas fa-tasks"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $completedTasks }}/{{ $totalTasks }}</span>
                <span class="stat-label">Tasks</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-success"><i class="fas fa-flag"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $completedMilestones }}/{{ $totalMilestones }}</span>
                <span class="stat-label">Milestones</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-info"><i class="fas fa-cogs"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $project->services->count() }}</span>
                <span class="stat-label">Services</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-warning"><i class="fas fa-file-alt"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $project->files->count() + $project->contracts->count() }}</span>
                <span class="stat-label">Documents</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-secondary"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ $project->budget ? number_format($project->budget, 0) : '-' }}</span>
                <span class="stat-label">Budget (OMR)</span>
            </div>
        </div>
    </div>

    <!-- 4 Tabs Only -->
    <div class="tabs-container">
        <ul class="nav nav-tabs custom-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                    <i class="fas fa-home me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tasks-milestones" type="button">
                    <i class="fas fa-tasks me-2"></i>Tasks & Milestones
                    <span class="tab-badge">{{ $totalTasks }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                    <i class="fas fa-folder me-2"></i>Documents
                    <span class="tab-badge">{{ $project->files->count() + $project->contracts->count() }}</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#services" type="button">
                    <i class="fas fa-cogs me-2"></i>Services
                    <span class="tab-badge">{{ $project->services->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Service Info -->
                        <div class="content-card mb-4">
                            <div class="card-header-simple">
                                <i class="fas fa-sitemap me-2"></i>Service Configuration
                            </div>
                            <div class="card-body-simple">
                                <div class="service-hierarchy">
                                    <div class="hierarchy-item">
                                        <span class="hierarchy-label">Main Service</span>
                                        <span class="hierarchy-value">
                                            <i class="fas fa-layer-group me-2 text-primary"></i>
                                            {{ $project->mainService->name ?? 'Not set' }}
                                        </span>
                                    </div>
                                    @if($project->subService)
                                        <div class="hierarchy-item">
                                            <span class="hierarchy-label">Sub Service</span>
                                            <span class="hierarchy-value">
                                                <i class="fas fa-arrow-right me-2 text-secondary"></i>
                                                {{ $project->subService->name }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($project->servicePackage)
                                        <div class="hierarchy-item">
                                            <span class="hierarchy-label">Package</span>
                                            <span class="hierarchy-value">
                                                <i class="fas fa-box me-2 text-info"></i>
                                                {{ $project->servicePackage->name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($project->description)
                            <div class="content-card mb-4">
                                <div class="card-header-simple">
                                    <i class="fas fa-align-left me-2"></i>Description
                                </div>
                                <div class="card-body-simple">
                                    <p class="mb-0">{{ $project->description }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Progress Overview -->
                        <div class="content-card">
                            <div class="card-header-simple">
                                <i class="fas fa-chart-line me-2"></i>Progress
                            </div>
                            <div class="card-body-simple">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">Task Completion</span>
                                        <span>{{ $taskProgress }}%</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $taskProgress }}%"></div>
                                    </div>
                                </div>
                                @if($totalMilestones > 0)
                                    @php $milestoneProgress = round(($completedMilestones / $totalMilestones) * 100); @endphp
                                    <div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="fw-semibold">Milestone Completion</span>
                                            <span>{{ $milestoneProgress }}%</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" style="width: {{ $milestoneProgress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Details -->
                        <div class="content-card mb-4">
                            <div class="card-header-simple">
                                <i class="fas fa-info-circle me-2"></i>Details
                            </div>
                            <div class="card-body-simple">
                                <div class="detail-list">
                                    <div class="detail-item">
                                        <span class="detail-label"><i class="fas fa-calendar-plus me-2"></i>Start Date</span>
                                        <span class="detail-value">{{ $project->start_date ? $project->start_date->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><i class="fas fa-calendar-check me-2"></i>End Date</span>
                                        <span class="detail-value {{ $project->end_date && $project->end_date->isPast() && $project->status !== 'completed' ? 'text-danger' : '' }}">
                                            {{ $project->end_date ? $project->end_date->format('M d, Y') : '-' }}
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><i class="fas fa-clock me-2"></i>Duration</span>
                                        <span class="detail-value">
                                            @if($project->start_date && $project->end_date)
                                                {{ $project->start_date->diffInDays($project->end_date) }} days
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><i class="fas fa-history me-2"></i>Created</span>
                                        <span class="detail-value">{{ $project->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label"><i class="fas fa-sync me-2"></i>Updated</span>
                                        <span class="detail-value">{{ $project->updated_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="content-card">
                            <div class="card-header-simple">
                                <i class="fas fa-stream me-2"></i>Timeline
                            </div>
                            <div class="card-body-simple">
                                <div class="timeline-mini">
                                    <div class="timeline-event">
                                        <div class="timeline-dot bg-primary"></div>
                                        <div class="timeline-content">
                                            <strong>Project Created</strong>
                                            <small>{{ $project->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @if($project->start_date)
                                        <div class="timeline-event">
                                            <div class="timeline-dot bg-success"></div>
                                            <div class="timeline-content">
                                                <strong>{{ $project->start_date->isPast() ? 'Started' : 'Starts' }}</strong>
                                                <small>{{ $project->start_date->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    @if($project->end_date)
                                        <div class="timeline-event">
                                            <div class="timeline-dot {{ $project->end_date->isPast() ? 'bg-danger' : 'bg-warning' }}"></div>
                                            <div class="timeline-content">
                                                <strong>{{ $project->end_date->isPast() ? 'Ended' : 'Ends' }}</strong>
                                                <small>{{ $project->end_date->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks & Milestones Tab -->
            <div class="tab-pane fade" id="tasks-milestones">
                <!-- Milestones Section -->
                <div class="section-header mb-3">
                    <h5 class="mb-0"><i class="fas fa-flag me-2"></i>Milestones</h5>
                    <div class="section-actions">
                        <form action="{{ route('admin.milestones.generate', $project) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-magic me-1"></i>Auto Generate
                            </button>
                        </form>
                        <a href="{{ route('admin.milestones.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add
                        </a>
                    </div>
                </div>

                @if($project->milestones->count() > 0)
                    <div class="row g-3 mb-4">
                        @foreach($project->milestones->sortBy('sort_order') as $milestone)
                            @php $progress = $milestone->calculateProgress(); @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="milestone-card">
                                    <div class="milestone-header">
                                        @if($milestone->status === 'completed')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($milestone->status === 'in_progress')
                                            <i class="fas fa-spinner text-primary"></i>
                                        @elseif($milestone->status === 'overdue')
                                            <i class="fas fa-exclamation-circle text-danger"></i>
                                        @else
                                            <i class="fas fa-flag text-secondary"></i>
                                        @endif
                                        <span class="milestone-title">{{ $milestone->title }}</span>
                                    </div>
                                    <div class="milestone-body">
                                        <div class="progress mb-2" style="height: 5px;">
                                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="milestone-meta">
                                            <span><i class="fas fa-tasks"></i> {{ $milestone->tasks->count() }} tasks</span>
                                            <span><i class="fas fa-calendar"></i> {{ $milestone->target_date?->format('M d') ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-sm mb-4">
                        <i class="fas fa-flag"></i>
                        <p>No milestones yet</p>
                    </div>
                @endif

                <!-- Tasks Section -->
                <div class="section-header mb-3">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Tasks ({{ $completedTasks }}/{{ $totalTasks }})</h5>
                    <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Task
                    </a>
                </div>

                @if($project->tasks->count() > 0)
                    <div class="task-list">
                        @foreach($project->tasks->sortBy('sort_order') as $task)
                            <a href="{{ route('admin.tasks.show', $task) }}" class="task-item">
                                <div class="task-status">
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
                                <div class="task-info">
                                    <div class="task-title">{{ $task->title }}</div>
                                    <div class="task-meta">
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
                                <div class="task-badges">
                                    <span class="badge-priority badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-sm">
                        <i class="fas fa-tasks"></i>
                        <p>No tasks yet</p>
                        <a href="{{ route('admin.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">Create First Task</a>
                    </div>
                @endif
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents">
                <!-- Contracts Section -->
                @if($project->contracts->count() > 0)
                    <div class="section-header mb-3">
                        <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Contracts</h5>
                    </div>
                    <div class="document-list mb-4">
                        @foreach($project->contracts as $contract)
                            <a href="{{ route('admin.contracts.show', $contract->id) }}" class="document-item">
                                <div class="document-icon">
                                    <i class="fas fa-file-contract text-primary"></i>
                                </div>
                                <div class="document-info">
                                    <div class="document-name">{{ $contract->title }}</div>
                                    <div class="document-meta">
                                        <span>{{ $contract->contract_number }}</span>
                                        <span class="badge bg-{{ $contract->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($contract->status) }}</span>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Files Section -->
                <div class="section-header mb-3">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Files</h5>
                </div>

                @if($project->files->count() > 0)
                    <div class="document-list">
                        @foreach($project->files as $file)
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
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas {{ $iconClass }}"></i>
                                </div>
                                <div class="document-info">
                                    <div class="document-name">{{ $file->original_name }}</div>
                                    <div class="document-meta">
                                        <span>{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                        <span>{{ $file->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state-sm">
                        <i class="fas fa-folder-open"></i>
                        <p>No files uploaded</p>
                    </div>
                @endif
            </div>

            <!-- Services Tab -->
            <div class="tab-pane fade" id="services">
                @php
                    $projectServices = $project->services()->with('service.serviceStage')->orderBy('sort_order')->get();
                    $groupedServices = $projectServices->groupBy(function($item) {
                        return $item->service->serviceStage->name ?? 'Uncategorized';
                    });
                @endphp

                @forelse($groupedServices as $stageName => $stageServices)
                    <div class="content-card mb-3">
                        <div class="card-header-simple d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-layer-group me-2"></i>{{ $stageName }}</span>
                            <span class="badge bg-light text-dark">{{ $stageServices->count() }}</span>
                        </div>
                        <div class="card-body-simple p-0">
                            <div class="service-list">
                                @foreach($stageServices as $projectService)
                                    <div class="service-item">
                                        <div class="service-check">
                                            @if($projectService->is_completed)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="far fa-circle text-muted"></i>
                                            @endif
                                        </div>
                                        <span class="service-name">{{ $projectService->service->name }}</span>
                                        <div class="service-badges">
                                            @if($projectService->is_from_package)
                                                <span class="badge-mini badge-package">Package</span>
                                            @else
                                                <span class="badge-mini badge-custom">Custom</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-sm">
                        <i class="fas fa-cogs"></i>
                        <p>No services assigned</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Header */
    .project-header {
        background: white;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .project-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .status-badge {
        padding: 5px 14px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending { background: #e0e0e0; color: #616161; }
    .status-in_progress { background: #e3f2fd; color: #1976d2; }
    .status-on_hold { background: #fff3e0; color: #f57c00; }
    .status-completed { background: #e8f5e9; color: #388e3c; }
    .status-cancelled { background: #ffebee; color: #c62828; }

    .project-meta {
        display: flex;
        gap: 20px;
        font-size: 13px;
        color: #6c757d;
    }

    .project-meta span i {
        margin-right: 5px;
        color: var(--secondary-color);
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .stat-info { display: flex; flex-direction: column; }
    .stat-value { font-size: 18px; font-weight: 700; color: var(--primary-color); }
    .stat-label { font-size: 11px; color: #6c757d; text-transform: uppercase; }

    /* Tabs */
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
        border-radius: 0;
    }

    .custom-tabs .nav-link:hover { color: var(--primary-color); background: rgba(243, 200, 135, 0.1); }
    .custom-tabs .nav-link.active { color: var(--primary-color); background: white; border-bottom: 3px solid var(--secondary-color); }

    .tab-badge {
        background: var(--secondary-color);
        color: var(--primary-color);
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 10px;
        font-weight: 600;
        margin-left: 5px;
    }

    .tab-content { padding: 20px; }

    /* Content Cards */
    .content-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header-simple {
        background: linear-gradient(135deg, var(--primary-color), #4a1a1f);
        color: white;
        padding: 12px 18px;
        font-weight: 600;
        font-size: 14px;
    }

    .card-body-simple { padding: 18px; }

    /* Service Hierarchy */
    .service-hierarchy { display: flex; flex-direction: column; gap: 12px; }
    .hierarchy-item { display: flex; align-items: center; gap: 15px; }
    .hierarchy-label { font-size: 12px; font-weight: 600; color: #6c757d; min-width: 100px; text-transform: uppercase; }
    .hierarchy-value { background: #f8f9fa; padding: 8px 15px; border-radius: 6px; font-weight: 500; color: var(--primary-color); flex: 1; }

    /* Details List */
    .detail-list { display: flex; flex-direction: column; }
    .detail-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { color: #6c757d; }
    .detail-value { font-weight: 600; color: var(--primary-color); }

    /* Mini Timeline */
    .timeline-mini { display: flex; flex-direction: column; gap: 15px; }
    .timeline-event { display: flex; gap: 12px; align-items: flex-start; }
    .timeline-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 5px; }
    .timeline-content { display: flex; flex-direction: column; }
    .timeline-content strong { font-size: 13px; color: var(--primary-color); }
    .timeline-content small { font-size: 11px; color: #6c757d; }

    /* Section Header */
    .section-header { display: flex; justify-content: space-between; align-items: center; }
    .section-actions { display: flex; gap: 8px; }

    /* Milestone Cards */
    .milestone-card { background: #f8f9fa; border-radius: 8px; overflow: hidden; }
    .milestone-header { padding: 12px 15px; background: white; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; gap: 10px; }
    .milestone-title { font-weight: 600; font-size: 14px; color: var(--primary-color); }
    .milestone-body { padding: 12px 15px; }
    .milestone-meta { display: flex; justify-content: space-between; font-size: 11px; color: #6c757d; }
    .milestone-meta i { margin-right: 4px; }

    /* Task List */
    .task-list { display: flex; flex-direction: column; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; }
    .task-item { display: flex; align-items: center; gap: 15px; padding: 12px 15px; text-decoration: none; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; }
    .task-item:last-child { border-bottom: none; }
    .task-item:hover { background: #f8f9fa; }
    .task-status { font-size: 18px; width: 24px; text-align: center; }
    .task-info { flex: 1; }
    .task-title { font-weight: 500; color: var(--primary-color); font-size: 14px; }
    .task-meta { font-size: 11px; color: #6c757d; display: flex; gap: 12px; margin-top: 3px; }
    .task-meta i { margin-right: 3px; }

    .badge-priority { font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: 600; text-transform: uppercase; }
    .badge-low { background: #e8f5e9; color: #388e3c; }
    .badge-medium { background: #fff3e0; color: #f57c00; }
    .badge-high { background: #ffebee; color: #c62828; }
    .badge-urgent { background: #c62828; color: white; }

    /* Document List */
    .document-list { display: flex; flex-direction: column; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; }
    .document-item { display: flex; align-items: center; gap: 15px; padding: 12px 15px; text-decoration: none; border-bottom: 1px solid #f0f0f0; }
    .document-item:last-child { border-bottom: none; }
    .document-item:hover { background: #f8f9fa; }
    .document-icon { font-size: 24px; width: 40px; text-align: center; }
    .document-info { flex: 1; }
    .document-name { font-weight: 500; color: var(--primary-color); font-size: 14px; }
    .document-meta { font-size: 11px; color: #6c757d; display: flex; gap: 15px; margin-top: 3px; }

    /* Service List */
    .service-list { display: flex; flex-direction: column; }
    .service-item { display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-bottom: 1px solid #f0f0f0; }
    .service-item:last-child { border-bottom: none; }
    .service-check { font-size: 16px; }
    .service-name { flex: 1; font-weight: 500; color: var(--primary-color); font-size: 14px; }
    .badge-mini { font-size: 9px; padding: 3px 7px; border-radius: 4px; font-weight: 600; text-transform: uppercase; }
    .badge-package { background: #e3f2fd; color: #1976d2; }
    .badge-custom { background: #fff3e0; color: #f57c00; }

    /* Empty State */
    .empty-state-sm { text-align: center; padding: 40px 20px; color: #6c757d; }
    .empty-state-sm i { font-size: 40px; margin-bottom: 10px; opacity: 0.3; }
    .empty-state-sm p { margin-bottom: 10px; }

    @media (max-width: 768px) {
        .project-header { flex-direction: column; gap: 15px; }
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .custom-tabs { overflow-x: auto; white-space: nowrap; }
    }
</style>
@endpush
