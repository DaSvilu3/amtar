/**
 * Task Dependencies - Visual Graph & Management
 */

document.addEventListener('DOMContentLoaded', function() {
    const dependencyGraph = document.getElementById('task-dependency-graph');

    if (!dependencyGraph) {
        return;
    }

    // Initialize dependency management
    initializeDependencyManager();
    initializeDependencyGraph();
});

function initializeDependencyManager() {
    const addDependencyBtn = document.getElementById('add-dependency-btn');
    const dependencyModal = document.getElementById('dependency-modal');
    const dependencySelect = document.getElementById('dependency-task-select');
    const saveDependencyBtn = document.getElementById('save-dependency-btn');

    if (!addDependencyBtn) return;

    // Open modal to add dependency
    addDependencyBtn.addEventListener('click', function() {
        if (dependencyModal) {
            dependencyModal.style.display = 'block';
        }
    });

    // Save dependency
    if (saveDependencyBtn) {
        saveDependencyBtn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const dependsOnTaskId = dependencySelect.value;
            const dependencyType = document.getElementById('dependency-type-select').value;

            if (!dependsOnTaskId) {
                showNotification('Please select a task', 'error');
                return;
            }

            // Send to server
            fetch(`/admin/tasks/${taskId}/dependencies`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    depends_on_task_id: dependsOnTaskId,
                    dependency_type: dependencyType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Dependency added successfully', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Failed to add dependency', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding dependency:', error);
                showNotification('Error adding dependency', 'error');
            });
        });
    }

    // Remove dependency
    document.querySelectorAll('.remove-dependency-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            const dependencyId = this.dataset.dependencyId;

            if (!confirm('Are you sure you want to remove this dependency?')) {
                return;
            }

            fetch(`/admin/tasks/${taskId}/dependencies/${dependencyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Dependency removed successfully', 'success');
                    location.reload();
                } else {
                    showNotification('Failed to remove dependency', 'error');
                }
            })
            .catch(error => {
                console.error('Error removing dependency:', error);
                showNotification('Error removing dependency', 'error');
            });
        });
    });
}

function initializeDependencyGraph() {
    const graphContainer = document.getElementById('task-dependency-graph');

    if (!graphContainer) return;

    const tasksData = JSON.parse(graphContainer.dataset.tasks || '[]');
    const dependencies = JSON.parse(graphContainer.dataset.dependencies || '[]');

    // Simple visual graph using Mermaid-like syntax
    renderDependencyGraph(graphContainer, tasksData, dependencies);
}

function renderDependencyGraph(container, tasks, dependencies) {
    // Create a simple flowchart visualization
    let graphHTML = '<div class="dependency-graph-container">';

    // Organize tasks by level (tasks with no dependencies first)
    const taskLevels = organizeTasksByLevel(tasks, dependencies);

    taskLevels.forEach((level, levelIndex) => {
        graphHTML += `<div class="graph-level" data-level="${levelIndex}">`;

        level.forEach(task => {
            const taskDeps = dependencies.filter(d => d.task_id === task.id);
            const hasDependencies = taskDeps.length > 0;

            graphHTML += `
                <div class="graph-task" data-task-id="${task.id}">
                    <div class="graph-task-card ${hasDependencies ? 'has-dependencies' : ''}">
                        <div class="graph-task-title">${task.title}</div>
                        <div class="graph-task-status status-${task.status}">
                            ${task.status}
                        </div>
                        ${hasDependencies ? '<i class="fas fa-link dependency-icon"></i>' : ''}
                    </div>
                </div>
            `;
        });

        graphHTML += '</div>';
    });

    graphHTML += '</div>';

    container.innerHTML = graphHTML;

    // Draw connections
    drawDependencyConnections(container, dependencies);
}

function organizeTasksByLevel(tasks, dependencies) {
    const levels = [];
    const processed = new Set();

    // Level 0: Tasks with no dependencies
    const level0 = tasks.filter(task =>
        !dependencies.some(d => d.task_id === task.id)
    );

    if (level0.length > 0) {
        levels.push(level0);
        level0.forEach(t => processed.add(t.id));
    }

    // Subsequent levels
    let currentLevel = 0;
    while (processed.size < tasks.length && currentLevel < 10) {
        const nextLevel = tasks.filter(task => {
            if (processed.has(task.id)) return false;

            const taskDeps = dependencies.filter(d => d.task_id === task.id);
            return taskDeps.every(d => processed.has(d.depends_on_task_id));
        });

        if (nextLevel.length === 0) break;

        levels.push(nextLevel);
        nextLevel.forEach(t => processed.add(t.id));
        currentLevel++;
    }

    return levels;
}

function drawDependencyConnections(container, dependencies) {
    // Simple SVG overlay for connections
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.style.position = 'absolute';
    svg.style.top = '0';
    svg.style.left = '0';
    svg.style.width = '100%';
    svg.style.height = '100%';
    svg.style.pointerEvents = 'none';
    svg.style.zIndex = '1';

    container.style.position = 'relative';
    container.appendChild(svg);

    dependencies.forEach(dep => {
        const fromTask = container.querySelector(`[data-task-id="${dep.depends_on_task_id}"]`);
        const toTask = container.querySelector(`[data-task-id="${dep.task_id}"]`);

        if (fromTask && toTask) {
            const fromRect = fromTask.getBoundingClientRect();
            const toRect = toTask.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', fromRect.right - containerRect.left);
            line.setAttribute('y1', fromRect.top + fromRect.height / 2 - containerRect.top);
            line.setAttribute('x2', toRect.left - containerRect.left);
            line.setAttribute('y2', toRect.top + toRect.height / 2 - containerRect.top);
            line.setAttribute('stroke', '#3b82f6');
            line.setAttribute('stroke-width', '2');
            line.setAttribute('marker-end', 'url(#arrowhead)');

            svg.appendChild(line);
        }
    });

    // Add arrowhead marker
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const marker = document.createElementNS('http://www.w3.org/2000/svg', 'marker');
    marker.setAttribute('id', 'arrowhead');
    marker.setAttribute('markerWidth', '10');
    marker.setAttribute('markerHeight', '10');
    marker.setAttribute('refX', '9');
    marker.setAttribute('refY', '3');
    marker.setAttribute('orient', 'auto');

    const polygon = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
    polygon.setAttribute('points', '0 0, 10 3, 0 6');
    polygon.setAttribute('fill', '#3b82f6');

    marker.appendChild(polygon);
    defs.appendChild(marker);
    svg.insertBefore(defs, svg.firstChild);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.add('show'), 10);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
