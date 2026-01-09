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
            @can('update', $project)
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            @endcan
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
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notes-calendar" type="button">
                    <i class="fas fa-sticky-note me-2"></i>Notes & Calendar
                    <span class="tab-badge">{{ $project->notes->count() }}</span>
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

                <!-- Task Documents Section -->
                @php
                    $taskFiles = \App\Models\File::where('entity_type', 'Task')
                        ->whereIn('entity_id', $project->tasks->pluck('id'))
                        ->with(['uploadedBy'])
                        ->latest()
                        ->get();
                @endphp
                @if($taskFiles->count() > 0)
                    <div class="section-header mb-3 mt-4">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Task Documents <span class="badge bg-secondary">{{ $taskFiles->count() }}</span></h5>
                    </div>
                    <div class="document-list">
                        @foreach($taskFiles as $file)
                            @php
                                $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
                                $iconClass = match(strtolower($extension)) {
                                    'pdf' => 'fa-file-pdf text-danger',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-primary',
                                    'doc', 'docx' => 'fa-file-word text-info',
                                    'xls', 'xlsx' => 'fa-file-excel text-success',
                                    'zip', 'rar' => 'fa-file-archive text-warning',
                                    'dwg', 'dxf' => 'fa-drafting-compass text-secondary',
                                    default => 'fa-file text-secondary'
                                };
                                $task = $project->tasks->find($file->entity_id);
                            @endphp
                            <div class="document-item">
                                <div class="document-icon">
                                    <i class="fas {{ $iconClass }}"></i>
                                </div>
                                <div class="document-info">
                                    <div class="document-name">{{ $file->original_name }}</div>
                                    <div class="document-meta">
                                        <span>{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                        <span>{{ $file->uploadedBy->name ?? 'Unknown' }}</span>
                                        <span>{{ $file->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($task)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-link me-1"></i>
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="text-decoration-none">{{ Str::limit($task->title, 40) }}</a>
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @endforeach
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

            <!-- Notes & Calendar Tab -->
            <div class="tab-pane fade" id="notes-calendar">
                <div class="row g-4">
                    <!-- Left Column - Notes -->
                    <div class="col-lg-5">
                        <div class="section-header mb-3">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes & Comments</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                <i class="fas fa-plus me-1"></i>Add Note
                            </button>
                        </div>

                        <!-- Pinned Notes -->
                        @php $pinnedNotes = $project->notes()->pinned()->with('user')->get(); @endphp
                        @if($pinnedNotes->count() > 0)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2"><i class="fas fa-thumbtack me-1"></i>Pinned</h6>
                                @foreach($pinnedNotes as $note)
                                    <div class="note-card pinned mb-2">
                                        <div class="note-header">
                                            <span class="note-user">{{ $note->user->name }}</span>
                                            <span class="note-date">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="note-content">{{ $note->content }}</div>
                                        <div class="note-actions">
                                            <button class="btn btn-sm btn-link" onclick="togglePin({{ $note->id }})">
                                                <i class="fas fa-thumbtack"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger" onclick="deleteNote({{ $note->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- All Notes -->
                        <div class="notes-list" id="notesList">
                            @php $regularNotes = $project->notes()->where('is_pinned', false)->with('user')->latest()->get(); @endphp
                            @forelse($regularNotes as $note)
                                <div class="note-card mb-2" data-note-id="{{ $note->id }}">
                                    <div class="note-header">
                                        <span class="note-user">{{ $note->user->name }}</span>
                                        <div>
                                            @if($note->type === 'reminder' && $note->reminder_date)
                                                <span class="badge bg-warning me-1"><i class="fas fa-bell"></i> {{ $note->reminder_date->format('M d') }}</span>
                                            @endif
                                            @if($note->color)
                                                <span class="note-color-dot bg-{{ $note->color }}"></span>
                                            @endif
                                            <span class="note-date">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="note-content">{{ $note->content }}</div>
                                    @if(auth()->id() === $note->user_id || auth()->user()->hasAnyRole(['administrator', 'project-manager']))
                                        <div class="note-actions">
                                            <button class="btn btn-sm btn-link" onclick="togglePin({{ $note->id }})">
                                                <i class="far fa-thumbtack"></i>
                                            </button>
                                            <button class="btn btn-sm btn-link text-danger" onclick="deleteNote({{ $note->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="empty-state-sm">
                                    <i class="fas fa-sticky-note"></i>
                                    <p>No notes yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right Column - Calendar -->
                    <div class="col-lg-7">
                        <div class="section-header mb-3">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Project Calendar</h5>
                            <div class="calendar-legend">
                                <span class="legend-item"><span class="legend-dot bg-primary"></span>Task</span>
                                <span class="legend-item"><span class="legend-dot bg-warning"></span>Milestone</span>
                                <span class="legend-item"><span class="legend-dot bg-info"></span>Note</span>
                            </div>
                        </div>
                        <div class="content-card">
                            <div id="projectCalendar"></div>
                        </div>

                        <!-- Upcoming Items -->
                        <div class="content-card mt-3">
                            <div class="card-header-simple">
                                <i class="fas fa-clock me-2"></i>Upcoming Deadlines
                            </div>
                            <div class="card-body-simple p-0">
                                @php
                                    $upcomingTasks = $project->tasks()
                                        ->whereNotNull('due_date')
                                        ->where('due_date', '>=', now())
                                        ->where('status', '!=', 'completed')
                                        ->orderBy('due_date')
                                        ->limit(5)
                                        ->get();
                                    $upcomingMilestones = $project->milestones()
                                        ->whereNotNull('target_date')
                                        ->where('target_date', '>=', now())
                                        ->where('status', '!=', 'completed')
                                        ->orderBy('target_date')
                                        ->limit(3)
                                        ->get();
                                @endphp
                                <div class="upcoming-list">
                                    @foreach($upcomingMilestones as $milestone)
                                        <div class="upcoming-item">
                                            <div class="upcoming-icon bg-warning"><i class="fas fa-flag"></i></div>
                                            <div class="upcoming-info">
                                                <strong>{{ $milestone->title }}</strong>
                                                <small>{{ $milestone->target_date->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @foreach($upcomingTasks as $task)
                                        <div class="upcoming-item">
                                            <div class="upcoming-icon bg-primary"><i class="fas fa-tasks"></i></div>
                                            <div class="upcoming-info">
                                                <a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a>
                                                <small>Due: {{ $task->due_date->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($upcomingTasks->isEmpty() && $upcomingMilestones->isEmpty())
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0">No upcoming deadlines</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sticky-note me-2"></i>Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.projects.notes.store', $project) }}" method="POST" id="addNoteForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="4" required placeholder="Write your note here..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type" id="noteType" onchange="toggleReminderDate()">
                                <option value="note">Note</option>
                                <option value="comment">Comment</option>
                                <option value="reminder">Reminder</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="reminderDateField" style="display: none;">
                            <label class="form-label">Reminder Date</label>
                            <input type="date" class="form-control" name="reminder_date" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Color (for calendar)</label>
                            <div class="color-picker">
                                <label class="color-option">
                                    <input type="radio" name="color" value="">
                                    <span class="color-dot bg-secondary"></span>
                                </label>
                                <label class="color-option">
                                    <input type="radio" name="color" value="red">
                                    <span class="color-dot bg-danger"></span>
                                </label>
                                <label class="color-option">
                                    <input type="radio" name="color" value="yellow">
                                    <span class="color-dot bg-warning"></span>
                                </label>
                                <label class="color-option">
                                    <input type="radio" name="color" value="green">
                                    <span class="color-dot bg-success"></span>
                                </label>
                                <label class="color-option">
                                    <input type="radio" name="color" value="blue">
                                    <span class="color-dot bg-primary"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="isPinned">
                                <label class="form-check-label" for="isPinned">
                                    <i class="fas fa-thumbtack me-1"></i>Pin this note
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Note
                    </button>
                </div>
            </form>
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

    /* Notes styles */
    .note-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px 15px;
        border-left: 3px solid var(--secondary-color);
        position: relative;
    }

    .note-card.pinned {
        border-left-color: var(--primary-color);
        background: #fff9e6;
    }

    .note-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .note-user {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 13px;
    }

    .note-date {
        font-size: 11px;
        color: #6c757d;
    }

    .note-content {
        color: #495057;
        font-size: 14px;
        white-space: pre-wrap;
    }

    .note-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .note-card:hover .note-actions {
        opacity: 1;
    }

    .note-color-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    /* Calendar styles */
    .calendar-legend {
        display: flex;
        gap: 15px;
        font-size: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }

    #projectCalendar {
        padding: 15px;
    }

    .fc .fc-toolbar-title {
        font-size: 16px !important;
        color: var(--primary-color);
    }

    .fc .fc-button-primary {
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .fc-event {
        cursor: pointer;
        border: none !important;
        font-size: 11px !important;
    }

    /* Color picker styles */
    .color-picker {
        display: flex;
        gap: 10px;
    }

    .color-option {
        cursor: pointer;
    }

    .color-option input {
        display: none;
    }

    .color-option .color-dot {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: block;
        border: 2px solid transparent;
        transition: transform 0.2s, border-color 0.2s;
    }

    .color-option input:checked + .color-dot {
        border-color: var(--primary-color);
        transform: scale(1.2);
    }

    /* Upcoming list */
    .upcoming-list {
        display: flex;
        flex-direction: column;
    }

    .upcoming-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-bottom: 1px solid #f0f0f0;
    }

    .upcoming-item:last-child {
        border-bottom: none;
    }

    .upcoming-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }

    .upcoming-info {
        display: flex;
        flex-direction: column;
    }

    .upcoming-info strong, .upcoming-info a {
        font-size: 13px;
        color: var(--primary-color);
        text-decoration: none;
    }

    .upcoming-info small {
        font-size: 11px;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .project-header { flex-direction: column; gap: 15px; }
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .custom-tabs { overflow-x: auto; white-space: nowrap; }
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Calendar when tab is shown
        const notesTab = document.querySelector('[data-bs-target="#notes-calendar"]');
        let calendarInitialized = false;

        notesTab.addEventListener('shown.bs.tab', function() {
            if (!calendarInitialized) {
                initCalendar();
                calendarInitialized = true;
            }
        });

        // If notes-calendar tab is active on load, init calendar
        if (notesTab.classList.contains('active')) {
            initCalendar();
            calendarInitialized = true;
        }
    });

    function initCalendar() {
        const calendarEl = document.getElementById('projectCalendar');
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            height: 'auto',
            events: function(info, successCallback, failureCallback) {
                fetch('{{ route("admin.projects.calendar-events", $project) }}?start=' + info.startStr + '&end=' + info.endStr)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data.events);
                    })
                    .catch(error => {
                        console.error('Error loading calendar events:', error);
                        failureCallback(error);
                    });
            },
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                } else if (info.event.extendedProps.noteId) {
                    // Show note details
                    alert('Note: ' + info.event.extendedProps.content);
                }
            },
            eventDidMount: function(info) {
                // Add tooltip
                if (info.event.extendedProps.content) {
                    info.el.setAttribute('title', info.event.extendedProps.content);
                }
            }
        });

        calendar.render();
    }

    // Toggle reminder date field
    function toggleReminderDate() {
        const noteType = document.getElementById('noteType').value;
        const reminderField = document.getElementById('reminderDateField');
        reminderField.style.display = noteType === 'reminder' ? 'block' : 'none';
    }

    // Toggle pin
    function togglePin(noteId) {
        fetch(`/admin/project-notes/${noteId}/toggle-pin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Delete note
    function deleteNote(noteId) {
        if (!confirm('Are you sure you want to delete this note?')) return;

        fetch(`/admin/project-notes/${noteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const noteCard = document.querySelector(`[data-note-id="${noteId}"]`);
                if (noteCard) {
                    noteCard.remove();
                } else {
                    location.reload();
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush
