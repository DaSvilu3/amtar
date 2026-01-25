<div class="dependency-section">
    <div class="dependency-header">
        <h4>
            <i class="fas fa-project-diagram"></i>
            Task Dependencies
        </h4>
        @can('update', $task)
        <button type="button" id="add-dependency-btn" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Add Dependency
        </button>
        @endcan
    </div>

    @if($task->dependencies->count() > 0)
    <div class="dependencies-list">
        <h5>This task depends on:</h5>
        <ul class="dependency-items">
            @foreach($task->dependencies as $dependency)
            <li class="dependency-item">
                <div class="dependency-info">
                    <i class="fas fa-link"></i>
                    <a href="{{ route('admin.tasks.show', $dependency) }}">
                        {{ $dependency->title }}
                    </a>
                    <span class="dependency-status status-{{ $dependency->status }}">
                        {{ ucfirst($dependency->status) }}
                    </span>
                </div>
                @can('update', $task)
                <button
                    type="button"
                    class="remove-dependency-btn"
                    data-task-id="{{ $task->id }}"
                    data-dependency-id="{{ $dependency->id }}"
                    title="Remove dependency"
                >
                    <i class="fas fa-times"></i>
                </button>
                @endcan
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($task->dependents->count() > 0)
    <div class="dependents-list">
        <h5>Tasks that depend on this:</h5>
        <ul class="dependency-items">
            @foreach($task->dependents as $dependent)
            <li class="dependency-item">
                <div class="dependency-info">
                    <i class="fas fa-arrow-right"></i>
                    <a href="{{ route('admin.tasks.show', $dependent) }}">
                        {{ $dependent->title }}
                    </a>
                    <span class="dependency-status status-{{ $dependent->status }}">
                        {{ ucfirst($dependent->status) }}
                    </span>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($task->dependencies->count() === 0 && $task->dependents->count() === 0)
    <div class="no-dependencies">
        <i class="fas fa-info-circle"></i>
        <p>No dependencies configured for this task</p>
    </div>
    @endif

    <!-- Dependency Graph Visualization -->
    @if($task->dependencies->count() > 0 || $task->dependents->count() > 0)
    <div class="dependency-graph-section">
        <h5>Dependency Graph</h5>
        <div
            id="task-dependency-graph"
            data-tasks="{{ json_encode($relatedTasks ?? []) }}"
            data-dependencies="{{ json_encode($dependencies ?? []) }}"
        >
            <!-- Graph will be rendered here by JavaScript -->
        </div>
    </div>
    @endif
</div>

<!-- Add Dependency Modal -->
<div id="dependency-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Add Task Dependency</h4>
            <button type="button" class="close-modal" onclick="document.getElementById('dependency-modal').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label for="dependency-task-select">This task depends on:</label>
                <select id="dependency-task-select" class="form-control">
                    <option value="">Select a task...</option>
                    @foreach($availableTasks ?? [] as $availableTask)
                        <option value="{{ $availableTask->id }}">
                            {{ $availableTask->title }} ({{ ucfirst($availableTask->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="dependency-type-select">Dependency Type:</label>
                <select id="dependency-type-select" class="form-control">
                    <option value="finish_to_start">Finish to Start (Default)</option>
                    <option value="start_to_start">Start to Start</option>
                    <option value="finish_to_finish">Finish to Finish</option>
                </select>
                <small class="form-text text-muted">
                    Finish to Start: The dependent task cannot start until this task finishes
                </small>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('dependency-modal').style.display='none'">
                Cancel
            </button>
            <button type="button" id="save-dependency-btn" class="btn btn-primary" data-task-id="{{ $task->id }}">
                <i class="fas fa-save"></i>
                Save Dependency
            </button>
        </div>
    </div>
</div>

<style>
.dependency-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.dependency-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.dependency-header h4 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dependencies-list, .dependents-list {
    margin-bottom: 20px;
}

.dependencies-list h5, .dependents-list h5 {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
}

.dependency-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dependency-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f9fafb;
    border-radius: 6px;
    margin-bottom: 8px;
}

.dependency-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.dependency-info a {
    color: #2f0e13;
    text-decoration: none;
    font-weight: 500;
}

.dependency-info a:hover {
    text-decoration: underline;
}

.dependency-status {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}

.remove-dependency-btn {
    background: #dc2626;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.remove-dependency-btn:hover {
    background: #b91c1c;
}

.no-dependencies {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

.no-dependencies i {
    font-size: 48px;
    margin-bottom: 10px;
}

.dependency-graph-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e5e7eb;
}

.dependency-graph-container {
    display: flex;
    flex-direction: column;
    gap: 30px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 8px;
    overflow-x: auto;
}

.graph-level {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.graph-task {
    min-width: 180px;
}

.graph-task-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    position: relative;
    transition: all 0.2s;
}

.graph-task-card.has-dependencies {
    border-color: #3b82f6;
}

.graph-task-card:hover {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.graph-task-title {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 13px;
}

.graph-task-status {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-block;
}

.dependency-icon {
    position: absolute;
    top: 8px;
    right: 8px;
    color: #3b82f6;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h4 {
    margin: 0;
}

.close-modal {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
</style>

<script src="{{ asset('js/task-dependencies.js') }}"></script>
