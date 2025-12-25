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
                        <p class="text-muted mb-2">Unassigned</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="autoAssignTask()">
                            <i class="fas fa-magic me-1"></i>Auto-Assign
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showSuggestions()">
                            <i class="fas fa-users me-1"></i>Suggestions
                        </button>
                    @endif
                </div>

                @if($task->reviewedBy)
                <div class="mb-3">
                    <label class="text-muted small">Reviewer</label>
                    <div class="d-flex align-items-center">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; margin-right: 10px;">
                            {{ strtoupper(substr($task->reviewedBy->name, 0, 2)) }}
                        </div>
                        <span>{{ $task->reviewedBy->name }}</span>
                    </div>
                </div>
                @endif

                @if($task->requires_review)
                <div class="mb-3">
                    <span class="badge bg-warning"><i class="fas fa-eye me-1"></i>Review Required</span>
                </div>
                @endif

                @if($task->review_notes)
                <div class="mb-3">
                    <label class="text-muted small">Review Notes</label>
                    <p class="mb-0 small">{{ $task->review_notes }}</p>
                </div>
                @endif

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

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .suggestion-card { transition: all 0.2s ease; cursor: pointer; }
    .suggestion-card:hover { transform: translateX(5px); background: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
    const suggestionsModal = new bootstrap.Modal(document.getElementById('suggestionsModal'));

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
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: 600; margin-right: 12px;">
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
        // You can implement manual assignment here
        alert('Manual assignment feature - assign to user ID: ' + userId);
        suggestionsModal.hide();
    }
</script>
@endpush
