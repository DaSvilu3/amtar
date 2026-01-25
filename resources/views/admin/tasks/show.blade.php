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
            @if(auth()->user()->hasAnyRole(['administrator', 'project-manager']))
            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('admin.tasks.index', ['project_id' => $task->project_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Alert Banners -->
    @if($task->isOverdue())
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <h5 class="mb-0">Task Overdue</h5>
                <small>This task was due {{ $task->due_date->diffForHumans() }}</small>
            </div>
        </div>
    @endif

    @if($task->status === 'review')
        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-clock fa-2x me-3"></i>
                <div>
                    <h5 class="mb-0">Pending Review</h5>
                    <small>
                        @if($task->reviewedBy)
                            Waiting for review by <strong>{{ $task->reviewedBy->name }}</strong>
                        @else
                            Waiting for reviewer assignment
                        @endif
                    </small>
                </div>
            </div>
            @if(auth()->id() === $task->reviewed_by || auth()->user()->hasAnyRole(['administrator', 'project-manager']))
                <div>
                    <button class="btn btn-success me-2" onclick="showApproveModal()">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                    <button class="btn btn-danger" onclick="showRejectModal()">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </div>
            @endif
        </div>
    @endif

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
                            @if($task->priority === 'urgent')<i class="fas fa-fire me-1"></i>@endif
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

                <!-- Action Buttons for Assignee -->
                @if($task->assigned_to === auth()->id())
                <div class="mb-4 p-3 bg-light rounded">
                    <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>My Actions</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @if($task->status === 'pending')
                            <button class="btn btn-primary" onclick="updateTaskStatus('in_progress')">
                                <i class="fas fa-play me-1"></i>Start Working
                            </button>
                        @endif

                        @if($task->status === 'in_progress')
                            @if($task->requires_review || auth()->user()->hasRole('engineer'))
                                <button class="btn btn-warning" onclick="showSubmitForReviewModal()">
                                    <i class="fas fa-paper-plane me-1"></i>Submit for Review
                                </button>
                            @else
                                <button class="btn btn-success" onclick="updateTaskStatus('completed')">
                                    <i class="fas fa-check me-1"></i>Mark Complete
                                </button>
                            @endif
                            <button class="btn btn-outline-secondary" onclick="showUpdateProgressModal()">
                                <i class="fas fa-percentage me-1"></i>Update Progress
                            </button>
                        @endif

                        @if($task->status === 'review' && $task->reviewed_at && $task->review_notes)
                            <div class="alert alert-info mb-0 w-100">
                                <strong>Feedback from Reviewer:</strong>
                                <p class="mb-0 mt-1">{{ $task->review_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

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

            <!-- Task Dependency Graph -->
            @if($task->dependencies->count() > 0 || $task->dependents->count() > 0)
            @include('admin.tasks.partials._dependency-graph')
            @endif

            <!-- Review History Card -->
            @if($task->requires_review || $task->reviewed_by || $task->reviewed_at)
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-clipboard-check me-2" style="color: var(--secondary-color);"></i>
                    Review Information
                </h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">Review Required</label>
                            <p class="mb-0">
                                @if($task->requires_review)
                                    <span class="badge bg-warning"><i class="fas fa-check me-1"></i>Yes</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small">Review Status</label>
                            <p class="mb-0">
                                @if($task->status === 'review')
                                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending Review</span>
                                @elseif($task->reviewed_at && $task->status === 'completed')
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>
                                @elseif($task->reviewed_at && $task->status === 'in_progress')
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Revision Requested</span>
                                @else
                                    <span class="badge bg-secondary">Not Submitted</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($task->reviewedBy)
                <div class="mb-3">
                    <label class="text-muted small">Reviewer</label>
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2">
                            {{ strtoupper(substr($task->reviewedBy->name, 0, 2)) }}
                        </div>
                        <span>{{ $task->reviewedBy->name }}</span>
                    </div>
                </div>
                @endif

                @if($task->reviewed_at)
                <div class="mb-3">
                    <label class="text-muted small">Reviewed At</label>
                    <p class="mb-0">{{ $task->reviewed_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif

                @if($task->review_notes)
                <div class="mb-0">
                    <label class="text-muted small">Review Notes</label>
                    <div class="p-3 bg-light rounded">
                        <i class="fas fa-quote-left text-muted me-2"></i>
                        {{ $task->review_notes }}
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Task Documents -->
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-file-alt me-2" style="color: var(--secondary-color);"></i>
                        Documents
                        @if($task->files->count() > 0)
                            <span class="badge bg-secondary ms-2">{{ $task->files->count() }}</span>
                        @endif
                    </h5>
                    @if($task->assigned_to === auth()->id() || auth()->user()->hasAnyRole(['administrator', 'project-manager']))
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                        <i class="fas fa-upload me-1"></i>Upload
                    </button>
                    @endif
                </div>

                @if($task->files->count() > 0)
                    <div class="file-list">
                        @foreach($task->files as $file)
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
                            @endphp
                            <div class="file-item d-flex align-items-center justify-content-between p-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <i class="fas {{ $iconClass }} fa-lg me-3"></i>
                                    <div>
                                        <div class="fw-medium small">{{ $file->original_name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ number_format($file->file_size / 1024, 1) }} KB
                                            &bull; {{ $file->uploadedBy->name ?? 'Unknown' }}
                                            &bull; {{ $file->created_at->format('M d, Y') }}
                                        </div>
                                        @if($file->description)
                                            <div class="text-muted small fst-italic">{{ $file->description }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if($file->uploaded_by === auth()->id() || $task->assigned_to === auth()->id() || auth()->user()->hasAnyRole(['administrator', 'project-manager']))
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteFile({{ $file->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0 small">No documents uploaded yet</p>
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
                            <div class="avatar-circle me-2">
                                {{ strtoupper(substr($task->assignedTo->name, 0, 2)) }}
                            </div>
                            <span>{{ $task->assignedTo->name }}</span>
                        </div>
                    @else
                        <p class="text-muted mb-2">Unassigned</p>
                        @can('update', $task)
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="autoAssignTask()">
                            <i class="fas fa-magic me-1"></i>Auto-Assign
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showSuggestions()">
                            <i class="fas fa-users me-1"></i>Suggestions
                        </button>
                        @endcan
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

            <!-- Quick Status Update (for admin/PM) -->
            @if(auth()->user()->hasAnyRole(['administrator', 'project-manager']))
            <div class="dashboard-card">
                <h5 class="mb-3" style="color: var(--primary-color);">
                    <i class="fas fa-bolt me-2" style="color: var(--secondary-color);"></i>
                    Quick Actions
                </h5>
                <div class="d-grid gap-2">
                    @if($task->status !== 'pending')
                        <button class="btn btn-outline-secondary btn-sm" onclick="updateTaskStatus('pending')">
                            <i class="fas fa-undo me-1"></i>Reset to Pending
                        </button>
                    @endif
                    @if($task->status !== 'in_progress')
                        <button class="btn btn-outline-primary btn-sm" onclick="updateTaskStatus('in_progress')">
                            <i class="fas fa-play me-1"></i>Mark In Progress
                        </button>
                    @endif
                    @if($task->status !== 'review')
                        <button class="btn btn-outline-warning btn-sm" onclick="updateTaskStatus('review')">
                            <i class="fas fa-eye me-1"></i>Send to Review
                        </button>
                    @endif
                    @if($task->status !== 'completed')
                        <button class="btn btn-outline-success btn-sm" onclick="updateTaskStatus('completed')">
                            <i class="fas fa-check me-1"></i>Mark Completed
                        </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Assignment Suggestions Modal -->
<div class="modal fade" id="suggestionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users me-2"></i>Assignment Suggestions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="suggestionsLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Finding best candidates...</p>
                </div>
                <div id="suggestionsList" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Submit for Review Modal -->
<div class="modal fade" id="submitReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Submit for Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You are about to submit this task for review. Once submitted, a reviewer will evaluate your work.</p>
                <div class="mb-3">
                    <label class="form-label">Select Reviewer (Optional)</label>
                    <select class="form-select" id="reviewerSelect">
                        <option value="">Auto-assign reviewer</option>
                        @foreach(\App\Models\User::where('is_active', true)->whereHas('roles', fn($q) => $q->whereIn('slug', ['administrator', 'project-manager']))->get() as $reviewer)
                            <option value="{{ $reviewer->id }}">{{ $reviewer->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Leave empty to auto-assign based on availability</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="submitNotes" rows="3" placeholder="Any notes for the reviewer..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitForReview()">
                    <i class="fas fa-paper-plane me-1"></i>Submit for Review
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-check-circle text-success me-2"></i>Approve Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this task? This will mark the task as completed.</p>
                <div class="mb-3">
                    <label class="form-label">Review Notes (Optional)</label>
                    <textarea class="form-control" id="approveNotes" rows="3" placeholder="Great work! or any feedback..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="approveTask()">
                    <i class="fas fa-check me-1"></i>Approve & Complete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="fas fa-times-circle text-danger me-2"></i>Request Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This will send the task back to the assignee for revision.</p>
                <div class="mb-3">
                    <label class="form-label">Feedback <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectNotes" rows="4" placeholder="Please explain what needs to be revised..." required></textarea>
                    <small class="text-muted">This feedback will be visible to the assignee</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="rejectTask()">
                    <i class="fas fa-undo me-1"></i>Request Revision
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-percentage me-2"></i>Update Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Progress: <span id="progressValue">{{ $task->progress }}</span>%</label>
                    <input type="range" class="form-range" id="progressSlider" min="0" max="100" step="5" value="{{ $task->progress }}" oninput="document.getElementById('progressValue').textContent = this.value">
                </div>
                <div class="mb-3">
                    <label class="form-label">Actual Hours Spent</label>
                    <input type="number" class="form-control" id="actualHours" value="{{ $task->actual_hours ?? 0 }}" min="0" step="0.5">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateProgress()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.tasks.upload-file', $task) }}" method="POST" enctype="multipart/form-data" id="uploadFileForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" required>
                        <small class="text-muted">Max file size: 10MB. Supported: PDF, DOC, XLS, Images, ZIP, CAD files</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Brief description of the document..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="fas fa-upload me-1"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete File Confirmation Modal -->
<div class="modal fade" id="deleteFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2 text-danger"></i>Delete File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteFileBtn">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .suggestion-card { transition: all 0.2s ease; cursor: pointer; }
    .suggestion-card:hover { transform: translateX(5px); background: #f8f9fa; }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--secondary-color);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    const suggestionsModal = new bootstrap.Modal(document.getElementById('suggestionsModal'));
    const submitReviewModal = new bootstrap.Modal(document.getElementById('submitReviewModal'));
    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));

    // Update task status
    function updateTaskStatus(status) {
        fetch('{{ route("admin.tasks.update-status", $task) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // Show submit for review modal
    function showSubmitForReviewModal() {
        submitReviewModal.show();
    }

    // Submit task for review
    function submitForReview() {
        const reviewerId = document.getElementById('reviewerSelect').value;

        fetch('{{ route("admin.tasks.submit-review", $task) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                reviewed_by: reviewerId || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                submitReviewModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Failed to submit for review');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // Show approve modal
    function showApproveModal() {
        approveModal.show();
    }

    // Approve task
    function approveTask() {
        const notes = document.getElementById('approveNotes').value;

        fetch('{{ route("admin.tasks.approve", $task) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                approveModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Failed to approve task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // Show reject modal
    function showRejectModal() {
        rejectModal.show();
    }

    // Reject task
    function rejectTask() {
        const notes = document.getElementById('rejectNotes').value;

        if (!notes.trim()) {
            alert('Please provide feedback for the revision request');
            return;
        }

        fetch('{{ route("admin.tasks.reject", $task) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                rejectModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Failed to reject task');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // Show update progress modal
    function showUpdateProgressModal() {
        progressModal.show();
    }

    // Update progress
    function updateProgress() {
        const progress = document.getElementById('progressSlider').value;
        const actualHours = document.getElementById('actualHours').value;

        fetch('{{ route("admin.tasks.update-progress", $task) }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                progress: progress,
                actual_hours: actualHours
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Failed to update progress');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating progress');
        });
    }

    // Auto-assign task
    function autoAssignTask() {
        if (!confirm('Auto-assign this task to the best available consultant?')) return;

        fetch('{{ route("admin.tasks.auto-assign", $task) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Task assigned to ' + data.assigned_to);
                location.reload();
            } else {
                alert(data.message || 'Could not find a suitable consultant');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }

    // Show assignment suggestions
    function showSuggestions() {
        document.getElementById('suggestionsLoading').style.display = 'block';
        document.getElementById('suggestionsList').style.display = 'none';
        suggestionsModal.show();

        fetch('{{ route("admin.tasks.suggestions", $task) }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('suggestionsLoading').style.display = 'none';
            const list = document.getElementById('suggestionsList');
            list.style.display = 'block';

            if (data.suggestions && data.suggestions.length > 0) {
                let html = '<div class="list-group">';
                data.suggestions.forEach(s => {
                    html += `
                        <div class="list-group-item suggestion-card" onclick="assignTo(${s.user_id})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        ${s.user_name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <strong>${s.user_name}</strong>
                                        <br><small class="text-muted">${s.user_email}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success mb-1">Score: ${s.score}</span>
                                    <br><small class="text-muted">${s.available_hours}h available | ${s.current_workload}h workload</small>
                                </div>
                            </div>
                            ${s.matching_skills.length > 0 ? '<div class="mt-2"><small class="text-muted">Skills: ' + s.matching_skills.join(', ') + '</small></div>' : ''}
                        </div>
                    `;
                });
                html += '</div>';
                list.innerHTML = html;
            } else {
                list.innerHTML = '<div class="alert alert-info">No suitable candidates found based on skills and availability.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('suggestionsList').innerHTML = '<div class="alert alert-danger">Error loading suggestions</div>';
        });
    }

    function assignTo(userId) {
        alert('Manual assignment feature - assign to user ID: ' + userId);
        suggestionsModal.hide();
    }

    // File upload handling
    const uploadFileModal = new bootstrap.Modal(document.getElementById('uploadFileModal'));
    const deleteFileModal = new bootstrap.Modal(document.getElementById('deleteFileModal'));
    let fileToDeleteId = null;

    // Handle form submit with loading state
    document.getElementById('uploadFileForm').addEventListener('submit', function(e) {
        const uploadBtn = document.getElementById('uploadBtn');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
    });

    // Delete file function
    function deleteFile(fileId) {
        fileToDeleteId = fileId;
        deleteFileModal.show();
    }

    // Confirm delete file
    document.getElementById('confirmDeleteFileBtn').addEventListener('click', function() {
        if (!fileToDeleteId) return;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

        fetch(`/admin/tasks/{{ $task->id }}/files/${fileToDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                deleteFileModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Failed to delete file');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the file');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-trash me-1"></i>Delete';
        });
    });
</script>
@endpush
