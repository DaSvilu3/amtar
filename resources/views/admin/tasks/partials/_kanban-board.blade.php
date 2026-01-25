<div id="kanban-board" class="kanban-board">
    <div class="kanban-header">
        <h3>Kanban Board</h3>
        <div class="view-toggle">
            <button class="view-toggle-btn active" data-view="kanban">
                <i class="fas fa-th"></i> Kanban
            </button>
            <button class="view-toggle-btn" data-view="list">
                <i class="fas fa-list"></i> List
            </button>
        </div>
    </div>

    <div class="kanban-columns">
        <!-- Pending Column -->
        <div class="kanban-column-wrapper">
            <div class="kanban-column-header">
                <h4>
                    <i class="fas fa-clock text-warning"></i>
                    Pending
                    <span class="task-count">{{ $tasks->where('status', 'pending')->count() }}</span>
                </h4>
            </div>
            <div class="kanban-column" data-status="pending">
                @foreach($tasks->where('status', 'pending') as $task)
                    @include('admin.tasks.partials._task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column-wrapper">
            <div class="kanban-column-header">
                <h4>
                    <i class="fas fa-spinner text-primary"></i>
                    In Progress
                    <span class="task-count">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                </h4>
            </div>
            <div class="kanban-column" data-status="in_progress">
                @foreach($tasks->where('status', 'in_progress') as $task)
                    @include('admin.tasks.partials._task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- Review Column -->
        <div class="kanban-column-wrapper">
            <div class="kanban-column-header">
                <h4>
                    <i class="fas fa-eye text-purple"></i>
                    Review
                    <span class="task-count">{{ $tasks->where('status', 'review')->count() }}</span>
                </h4>
            </div>
            <div class="kanban-column" data-status="review">
                @foreach($tasks->where('status', 'review') as $task)
                    @include('admin.tasks.partials._task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- Completed Column -->
        <div class="kanban-column-wrapper">
            <div class="kanban-column-header">
                <h4>
                    <i class="fas fa-check-circle text-success"></i>
                    Completed
                    <span class="task-count">{{ $tasks->where('status', 'completed')->count() }}</span>
                </h4>
            </div>
            <div class="kanban-column" data-status="completed">
                @foreach($tasks->where('status', 'completed') as $task)
                    @include('admin.tasks.partials._task-card', ['task' => $task])
                @endforeach
            </div>
        </div>

        <!-- On Hold Column -->
        <div class="kanban-column-wrapper">
            <div class="kanban-column-header">
                <h4>
                    <i class="fas fa-pause-circle text-secondary"></i>
                    On Hold
                    <span class="task-count">{{ $tasks->where('status', 'on_hold')->count() }}</span>
                </h4>
            </div>
            <div class="kanban-column" data-status="on_hold">
                @foreach($tasks->where('status', 'on_hold') as $task)
                    @include('admin.tasks.partials._task-card', ['task' => $task])
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.kanban-board {
    background: #f8fafc;
    border-radius: 8px;
    padding: 20px;
}

.kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.view-toggle {
    display: flex;
    gap: 10px;
}

.view-toggle-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.view-toggle-btn.active {
    background: #2f0e13;
    color: white;
    border-color: #2f0e13;
}

.kanban-columns {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    overflow-x: auto;
}

.kanban-column-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.kanban-column-header {
    padding: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.kanban-column-header h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.task-count {
    background: #e5e7eb;
    color: #6b7280;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: auto;
}

.kanban-column {
    min-height: 400px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.task-ghost {
    opacity: 0.4;
    background: #e5e7eb;
}

.task-dragging {
    opacity: 0.8;
    transform: rotate(2deg);
}

.task-chosen {
    cursor: grabbing;
}

.task-updating {
    opacity: 0.6;
    pointer-events: none;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 1000;
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.3s;
}

.notification.show {
    opacity: 1;
    transform: translateY(0);
}

.notification-success {
    background: #10b981;
    color: white;
}

.notification-error {
    background: #dc2626;
    color: white;
}

@media (max-width: 768px) {
    .kanban-columns {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Load SortableJS from CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
