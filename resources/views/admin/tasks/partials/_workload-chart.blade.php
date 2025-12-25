{{-- Team Workload Visualization Component --}}
<div class="workload-chart-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Team Workload</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshWorkload()">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <div id="workloadChartContent">
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2">Loading team workload...</span>
        </div>
    </div>
</div>

<style>
.workload-bar {
    height: 24px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.workload-bar-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.workload-bar-fill.low {
    background: linear-gradient(90deg, #28a745, #34ce57);
}

.workload-bar-fill.medium {
    background: linear-gradient(90deg, #ffc107, #ffda6a);
}

.workload-bar-fill.high {
    background: linear-gradient(90deg, #fd7e14, #ff922b);
}

.workload-bar-fill.critical {
    background: linear-gradient(90deg, #dc3545, #ea868f);
}

.workload-user-row {
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    transition: background 0.2s;
}

.workload-user-row:hover {
    background: rgba(0,0,0,0.03);
}

.workload-percentage {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    font-weight: 600;
    color: #333;
}

.utilization-badge {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadWorkloadData();
});

function loadWorkloadData() {
    fetch('{{ route("admin.api.team-workload") }}')
        .then(response => response.json())
        .then(data => renderWorkloadChart(data))
        .catch(error => {
            console.error('Error loading workload:', error);
            document.getElementById('workloadChartContent').innerHTML =
                '<div class="alert alert-danger">Failed to load workload data</div>';
        });
}

function refreshWorkload() {
    document.getElementById('workloadChartContent').innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span class="ms-2">Refreshing...</span>
        </div>
    `;
    loadWorkloadData();
}

function renderWorkloadChart(users) {
    const container = document.getElementById('workloadChartContent');

    if (!users || users.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-3">No team members found</p>';
        return;
    }

    // Sort by utilization (highest first)
    users.sort((a, b) => b.utilization - a.utilization);

    let html = '<div class="workload-list">';

    users.forEach(user => {
        const utilizationClass = getUtilizationClass(user.utilization);
        const badgeClass = getBadgeClass(user.utilization);

        html += `
            <div class="workload-user-row mb-3" onclick="filterTasksByUser(${user.id}, '${user.name}')">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2" style="width: 28px; height: 28px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px;">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                        <span class="fw-medium">${user.name}</span>
                    </div>
                    <span class="utilization-badge ${badgeClass}">
                        ${user.allocated}h / ${user.capacity}h
                    </span>
                </div>
                <div class="workload-bar">
                    <div class="workload-bar-fill ${utilizationClass}" style="width: ${Math.min(100, user.utilization)}%"></div>
                    <span class="workload-percentage">${user.utilization}%</span>
                </div>
            </div>
        `;
    });

    html += '</div>';

    // Summary
    const avgUtilization = Math.round(users.reduce((sum, u) => sum + u.utilization, 0) / users.length);
    const overloaded = users.filter(u => u.utilization > 90).length;
    const available = users.filter(u => u.utilization < 70).length;

    html += `
        <div class="workload-summary border-top pt-3 mt-3">
            <div class="row text-center small">
                <div class="col-4">
                    <div class="text-muted">Avg Utilization</div>
                    <strong class="${getTextClass(avgUtilization)}">${avgUtilization}%</strong>
                </div>
                <div class="col-4">
                    <div class="text-muted">Overloaded</div>
                    <strong class="text-danger">${overloaded}</strong>
                </div>
                <div class="col-4">
                    <div class="text-muted">Available</div>
                    <strong class="text-success">${available}</strong>
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
}

function getUtilizationClass(utilization) {
    if (utilization < 50) return 'low';
    if (utilization < 70) return 'medium';
    if (utilization < 90) return 'high';
    return 'critical';
}

function getBadgeClass(utilization) {
    if (utilization < 50) return 'bg-success bg-opacity-10 text-success';
    if (utilization < 70) return 'bg-warning bg-opacity-10 text-warning';
    if (utilization < 90) return 'bg-orange bg-opacity-10 text-orange';
    return 'bg-danger bg-opacity-10 text-danger';
}

function getTextClass(utilization) {
    if (utilization < 50) return 'text-success';
    if (utilization < 70) return 'text-warning';
    if (utilization < 90) return 'text-orange';
    return 'text-danger';
}

function filterTasksByUser(userId, userName) {
    // Redirect to tasks index with user filter
    window.location.href = `{{ route('admin.tasks.index') }}?assigned_to=${userId}`;
}
</script>
