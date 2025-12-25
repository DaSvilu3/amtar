@extends('layouts.admin')

@section('title', 'Pending Reviews')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Pending Reviews</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
                    <li class="breadcrumb-item active">Pending Reviews</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="dashboard-card">
        @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Project</th>
                            <th>Submitted By</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                            <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                    @if($task->projectService)
                                        <br><small class="text-muted">{{ $task->projectService->service->name ?? '' }}</small>
                                    @endif
                                </td>
                                <td>{{ $task->project->name ?? '-' }}</td>
                                <td>
                                    @if($task->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; margin-right: 8px;">
                                                {{ strtoupper(substr($task->assignedTo->name, 0, 2)) }}
                                            </div>
                                            {{ $task->assignedTo->name }}
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : ($task->priority === 'medium' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->due_date)
                                        <span class="{{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" onclick="approveTask({{ $task->id }})" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="showRejectModal({{ $task->id }})" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No pending reviews</h5>
                <p class="text-muted">All tasks assigned to you for review have been processed.</p>
            </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle text-success me-2"></i>Approve Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Review Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about the review..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-times-circle text-danger me-2"></i>Request Revisions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Revision Notes <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Explain what needs to be revised..." required></textarea>
                        <small class="text-muted">Please provide clear feedback on what needs to be changed.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-undo me-2"></i>Request Revisions</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .table thead { background-color: var(--primary-color); color: white; }
</style>
@endpush

@push('scripts')
<script>
    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    let currentTaskId = null;

    function approveTask(taskId) {
        currentTaskId = taskId;
        document.getElementById('approveForm').action = `/admin/tasks/${taskId}/approve`;
        approveModal.show();
    }

    function showRejectModal(taskId) {
        currentTaskId = taskId;
        document.getElementById('rejectForm').action = `/admin/tasks/${taskId}/reject`;
        rejectModal.show();
    }

    // Handle form submissions via AJAX
    document.getElementById('approveForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReviewAction(this, 'Task approved successfully');
    });

    document.getElementById('rejectForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitReviewAction(this, 'Task sent back for revisions');
    });

    function submitReviewAction(form, successMessage) {
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the request');
        });
    }
</script>
@endpush
