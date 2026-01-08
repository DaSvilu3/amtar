@extends('layouts.admin')

@section('title', 'Approvals')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Approvals</h1>
        <p class="text-muted mb-0">Review and approve submitted tasks and milestones</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .approval-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-icon.pending { background: rgba(255, 152, 0, 0.15); color: #ff9800; }
    .stat-icon.milestone { background: rgba(156, 39, 176, 0.15); color: #9c27b0; }
    .stat-icon.approved { background: rgba(76, 175, 80, 0.15); color: #4caf50; }
    .stat-icon.rejected { background: rgba(244, 67, 54, 0.15); color: #f44336; }

    .stat-info h3 {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .stat-info p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .approval-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 0;
    }

    .approval-tab {
        padding: 12px 20px;
        background: none;
        border: none;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -1px;
        transition: all 0.2s;
    }

    .approval-tab:hover {
        color: #1e293b;
    }

    .approval-tab.active {
        color: var(--primary-color, #2f0e13);
        border-bottom-color: var(--primary-color, #2f0e13);
    }

    .approval-tab .badge {
        margin-left: 8px;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .approval-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .approval-item {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        transition: background 0.2s;
    }

    .approval-item:last-child {
        border-bottom: none;
    }

    .approval-item:hover {
        background: #f8fafc;
    }

    .priority-indicator {
        width: 4px;
        height: 100%;
        min-height: 60px;
        border-radius: 2px;
        flex-shrink: 0;
    }

    .priority-indicator.urgent { background: #f44336; }
    .priority-indicator.high { background: #ff9800; }
    .priority-indicator.medium { background: #2196f3; }
    .priority-indicator.low { background: #9e9e9e; }

    .approval-content {
        flex: 1;
        min-width: 0;
    }

    .approval-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }

    .approval-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .approval-title a {
        color: inherit;
        text-decoration: none;
    }

    .approval-title a:hover {
        color: var(--primary-color, #2f0e13);
    }

    .approval-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 13px;
        color: #64748b;
    }

    .approval-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .approval-meta i {
        font-size: 12px;
    }

    .approval-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge-priority {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-priority.urgent { background: #ffebee; color: #c62828; }
    .badge-priority.high { background: #fff3e0; color: #ef6c00; }
    .badge-priority.medium { background: #e3f2fd; color: #1565c0; }
    .badge-priority.low { background: #f5f5f5; color: #616161; }

    .approval-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .btn-approve, .btn-reject {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-approve {
        background: #4caf50;
        color: white;
    }

    .btn-approve:hover {
        background: #43a047;
    }

    .btn-reject {
        background: #f5f5f5;
        color: #f44336;
    }

    .btn-reject:hover {
        background: #ffebee;
    }

    .btn-view {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        background: none;
        border: 1px solid #e2e8f0;
        color: #64748b;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-view:hover {
        border-color: var(--primary-color, #2f0e13);
        color: var(--primary-color, #2f0e13);
    }

    .submitter-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .submitter-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color, #2f0e13), #5a2a30);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .submitter-name {
        font-size: 13px;
        color: #64748b;
    }

    .submitter-name strong {
        color: #1e293b;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 48px;
        color: #e2e8f0;
        margin-bottom: 16px;
    }

    .empty-state h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .history-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .history-item:last-child {
        border-bottom: none;
    }

    .history-status {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .history-status.approved {
        background: rgba(76, 175, 80, 0.15);
        color: #4caf50;
    }

    .history-status.rejected {
        background: rgba(244, 67, 54, 0.15);
        color: #f44336;
    }

    .history-content {
        flex: 1;
    }

    .history-title {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        margin: 0;
    }

    .history-meta {
        font-size: 12px;
        color: #64748b;
    }

    .history-time {
        font-size: 12px;
        color: #94a3b8;
    }

    @media (max-width: 1024px) {
        .approval-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .approval-stats {
            grid-template-columns: 1fr;
        }

        .approval-header {
            flex-direction: column;
        }

        .approval-tabs {
            overflow-x: auto;
        }
    }
</style>

<!-- Stats Cards -->
<div class="approval-stats">
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['pending_tasks'] }}</h3>
            <p>Pending Task Reviews</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon milestone">
            <i class="fas fa-flag"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['pending_milestones'] }}</h3>
            <p>Pending Milestones</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon approved">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['approved_today'] }}</h3>
            <p>Approved Today</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rejected">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <h3>{{ $stats['rejected_today'] }}</h3>
            <p>Rejected Today</p>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="approval-tabs">
    <button class="approval-tab active" data-tab="pending-tasks">
        Pending Tasks
        @if($pendingTaskApprovals->count() > 0)
            <span class="badge bg-warning text-dark">{{ $pendingTaskApprovals->count() }}</span>
        @endif
    </button>
    <button class="approval-tab" data-tab="pending-milestones">
        Pending Milestones
        @if($pendingMilestoneApprovals->count() > 0)
            <span class="badge bg-purple text-white" style="background: #9c27b0;">{{ $pendingMilestoneApprovals->count() }}</span>
        @endif
    </button>
    <button class="approval-tab" data-tab="history">
        Recent History
    </button>
</div>

<!-- Pending Tasks Tab -->
<div class="tab-content active" id="pending-tasks">
    <div class="approval-card">
        @forelse($pendingTaskApprovals as $task)
            <div class="approval-item">
                <div class="priority-indicator {{ $task->priority }}"></div>
                <div class="approval-content">
                    <div class="approval-header">
                        <div>
                            <h4 class="approval-title">
                                <a href="{{ route('admin.tasks.show', $task) }}">{{ $task->title }}</a>
                            </h4>
                            <div class="approval-meta">
                                <span><i class="fas fa-project-diagram"></i> {{ $task->project->name ?? 'No Project' }}</span>
                                @if($task->due_date)
                                    <span><i class="fas fa-calendar"></i> Due {{ $task->due_date->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="approval-badges">
                            <span class="badge-priority {{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                        </div>
                    </div>

                    <div class="submitter-info">
                        <div class="submitter-avatar">
                            {{ $task->assignedTo ? strtoupper(substr($task->assignedTo->name, 0, 2)) : '?' }}
                        </div>
                        <span class="submitter-name">
                            Submitted by <strong>{{ $task->assignedTo->name ?? 'Unknown' }}</strong>
                            @if($task->updated_at)
                                &middot; {{ $task->updated_at->diffForHumans() }}
                            @endif
                        </span>
                    </div>

                    <div class="approval-actions">
                        <form action="{{ route('admin.tasks.approve', $task) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn-reject" onclick="openRejectModal({{ $task->id }}, '{{ addslashes($task->title) }}')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                        <a href="{{ route('admin.tasks.show', $task) }}" class="btn-view">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <h4>No Pending Task Approvals</h4>
                <p>All submitted tasks have been reviewed.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Pending Milestones Tab -->
<div class="tab-content" id="pending-milestones">
    <div class="approval-card">
        @forelse($pendingMilestoneApprovals as $milestone)
            <div class="approval-item">
                <div class="priority-indicator high"></div>
                <div class="approval-content">
                    <div class="approval-header">
                        <div>
                            <h4 class="approval-title">{{ $milestone->name }}</h4>
                            <div class="approval-meta">
                                <span><i class="fas fa-project-diagram"></i> {{ $milestone->project->name ?? 'No Project' }}</span>
                                @if($milestone->target_date)
                                    <span><i class="fas fa-calendar"></i> Target {{ $milestone->target_date->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($milestone->description)
                        <p style="font-size: 13px; color: #64748b; margin: 10px 0;">{{ Str::limit($milestone->description, 150) }}</p>
                    @endif

                    <div class="approval-actions">
                        <form action="{{ route('admin.milestones.update', $milestone) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check"></i> Mark Complete
                            </button>
                        </form>
                        <a href="{{ route('admin.projects.show', $milestone->project_id) }}" class="btn-view">
                            <i class="fas fa-eye"></i> View Project
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-flag-checkered"></i>
                <h4>No Pending Milestone Approvals</h4>
                <p>All milestones are up to date.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- History Tab -->
<div class="tab-content" id="history">
    <div class="approval-card">
        <h5 style="padding: 20px 24px 0; margin: 0; color: #4caf50;">
            <i class="fas fa-check-circle"></i> Recently Approved
        </h5>
        @forelse($recentlyApproved as $task)
            <div class="history-item">
                <div class="history-status approved">
                    <i class="fas fa-check"></i>
                </div>
                <div class="history-content">
                    <h5 class="history-title">{{ $task->title }}</h5>
                    <p class="history-meta">
                        {{ $task->project->name ?? 'No Project' }}
                        &middot; By {{ $task->assignedTo->name ?? 'Unknown' }}
                        @if($task->reviewedBy)
                            &middot; Reviewed by {{ $task->reviewedBy->name }}
                        @endif
                    </p>
                </div>
                <span class="history-time">{{ $task->reviewed_at?->diffForHumans() ?? $task->updated_at->diffForHumans() }}</span>
            </div>
        @empty
            <div class="history-item">
                <p style="color: #64748b; margin: 0;">No recently approved tasks.</p>
            </div>
        @endforelse

        <h5 style="padding: 20px 24px 0; margin: 0; color: #f44336; border-top: 1px solid #f1f5f9;">
            <i class="fas fa-times-circle"></i> Recently Rejected
        </h5>
        @forelse($recentlyRejected as $task)
            <div class="history-item">
                <div class="history-status rejected">
                    <i class="fas fa-times"></i>
                </div>
                <div class="history-content">
                    <h5 class="history-title">{{ $task->title }}</h5>
                    <p class="history-meta">
                        {{ $task->project->name ?? 'No Project' }}
                        &middot; By {{ $task->assignedTo->name ?? 'Unknown' }}
                    </p>
                </div>
                <span class="history-time">{{ $task->updated_at->diffForHumans() }}</span>
            </div>
        @empty
            <div class="history-item">
                <p style="color: #64748b; margin: 0;">No recently rejected tasks.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Rejecting: <strong id="rejectTaskTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required
                            placeholder="Please provide feedback for the assignee..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tab switching
    document.querySelectorAll('.approval-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            document.querySelectorAll('.approval-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding content
            const tabId = this.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Reject modal
    function openRejectModal(taskId, taskTitle) {
        document.getElementById('rejectTaskTitle').textContent = taskTitle;
        document.getElementById('rejectForm').action = `/admin/tasks/${taskId}/reject`;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>
@endsection
