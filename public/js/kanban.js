/**
 * Kanban Board - Drag & Drop Functionality
 * Using SortableJS for drag and drop
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Kanban board if it exists
    const kanbanBoard = document.getElementById('kanban-board');

    if (!kanbanBoard) {
        return;
    }

    // Get all kanban columns
    const columns = document.querySelectorAll('.kanban-column');

    if (columns.length === 0) {
        return;
    }

    // Initialize Sortable for each column
    columns.forEach(column => {
        new Sortable(column, {
            group: 'kanban-tasks',
            animation: 150,
            ghostClass: 'task-ghost',
            dragClass: 'task-dragging',
            chosenClass: 'task-chosen',
            handle: '.task-card',
            onEnd: function(evt) {
                // Get task information
                const taskId = evt.item.dataset.taskId;
                const newStatus = evt.to.dataset.status;
                const position = evt.newIndex;

                // Show loading indicator
                evt.item.classList.add('task-updating');

                // Send update to server
                fetch('/admin/tasks/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        status: newStatus,
                        position: position
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update task status badge
                        updateTaskStatusBadge(evt.item, newStatus);

                        // Show success notification
                        showNotification('Task status updated successfully', 'success');
                    } else {
                        // Revert the move
                        evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                        showNotification('Failed to update task status', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error updating task:', error);
                    // Revert the move
                    evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex]);
                    showNotification('Error updating task status', 'error');
                })
                .finally(() => {
                    evt.item.classList.remove('task-updating');
                });
            }
        });
    });

    // Update task status badge after drag
    function updateTaskStatusBadge(taskElement, newStatus) {
        const badge = taskElement.querySelector('.task-status-badge');
        if (badge) {
            // Remove old status classes
            badge.className = 'task-status-badge';

            // Add new status class
            badge.classList.add('status-' + newStatus);

            // Update text
            badge.textContent = newStatus.replace('_', ' ').toUpperCase();
        }
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        // Add to body
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Toggle between Kanban and List view
    const viewToggleButtons = document.querySelectorAll('.view-toggle-btn');
    const kanbanView = document.getElementById('kanban-view');
    const listView = document.getElementById('list-view');

    viewToggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;

            // Update active button
            viewToggleButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Toggle views
            if (view === 'kanban') {
                if (kanbanView) kanbanView.style.display = 'block';
                if (listView) listView.style.display = 'none';
                localStorage.setItem('tasks-view', 'kanban');
            } else {
                if (kanbanView) kanbanView.style.display = 'none';
                if (listView) listView.style.display = 'block';
                localStorage.setItem('tasks-view', 'list');
            }
        });
    });

    // Restore saved view preference
    const savedView = localStorage.getItem('tasks-view');
    if (savedView) {
        const button = document.querySelector(`.view-toggle-btn[data-view="${savedView}"]`);
        if (button) {
            button.click();
        }
    }
});
