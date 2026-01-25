<div class="task-card" data-task-id="{{ $task->id }}">
    <div class="task-card-header">
        <span class="task-status-badge status-{{ $task->status }}">
            {{ ucfirst($task->status) }}
        </span>
        <span class="task-priority priority-{{ $task->priority }}">
            <i class="fas fa-flag"></i>
            {{ ucfirst($task->priority) }}
        </span>
    </div>

    <h5 class="task-title">
        <a href="{{ route('admin.tasks.show', $task) }}">
            {{ $task->title }}
        </a>
    </h5>

    <div class="task-meta">
        <div class="task-project">
            <i class="fas fa-project-diagram"></i>
            {{ Str::limit($task->project->name ?? 'No Project', 20) }}
        </div>

        @if($task->assignedTo)
        <div class="task-assignee">
            <i class="fas fa-user"></i>
            {{ Str::limit($task->assignedTo->name, 15) }}
        </div>
        @endif

        @if($task->due_date)
        <div class="task-due-date {{ $task->due_date->isPast() ? 'overdue' : '' }}">
            <i class="fas fa-calendar"></i>
            {{ $task->due_date->format('M d') }}
        </div>
        @endif
    </div>

    <div class="task-progress">
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $task->progress }}%"></div>
        </div>
        <span class="progress-text">{{ $task->progress }}%</span>
    </div>

    @if($task->estimated_hours)
    <div class="task-hours">
        <i class="fas fa-clock"></i>
        {{ $task->estimated_hours }}h
        @if($task->actual_hours)
            / {{ $task->actual_hours }}h
        @endif
    </div>
    @endif
</div>

<style>
.task-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    cursor: grab;
    transition: all 0.2s;
}

.task-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.task-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.task-status-badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-in_progress { background: #dbeafe; color: #1e40af; }
.status-review { background: #ede9fe; color: #5b21b6; }
.status-completed { background: #d1fae5; color: #065f46; }
.status-on_hold { background: #f3f4f6; color: #374151; }

.task-priority {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.priority-urgent { background: #fee2e2; color: #991b1b; }
.priority-high { background: #fed7aa; color: #9a3412; }
.priority-normal { background: #dbeafe; color: #1e40af; }
.priority-low { background: #f3f4f6; color: #6b7280; }

.task-title {
    font-size: 14px;
    font-weight: 600;
    margin: 8px 0;
}

.task-title a {
    color: #1f2937;
    text-decoration: none;
}

.task-title a:hover {
    color: #2f0e13;
}

.task-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 10px 0;
    font-size: 12px;
    color: #6b7280;
}

.task-meta > div {
    display: flex;
    align-items: center;
    gap: 6px;
}

.task-due-date.overdue {
    color: #dc2626;
    font-weight: 600;
}

.task-progress {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    transition: width 0.3s;
}

.progress-text {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
}

.task-hours {
    margin-top: 8px;
    font-size: 11px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 4px;
}
</style>
