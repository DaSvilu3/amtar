@extends('layouts.admin')

@section('title', 'Roles & Permissions Matrix')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Roles & Permissions Matrix</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Permissions Matrix</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Role
            </a>
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-4 align-items-center">
                <span class="fw-medium">Legend:</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="permission-indicator granted"></div>
                    <span class="small">Permission Granted</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="permission-indicator denied"></div>
                    <span class="small">Permission Denied</span>
                </div>
                <div class="ms-auto text-muted small">
                    <i class="fas fa-info-circle me-1"></i>Click on any checkbox to toggle permission
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Summary Cards -->
    <div class="row mb-4">
        @foreach($roles as $role)
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="card h-100 role-card" data-role-id="{{ $role->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">{{ $role->name }}</h6>
                        <span class="badge bg-primary">{{ $role->users_count }} users</span>
                    </div>
                    <p class="text-muted small mb-2">{{ $role->description ?? 'No description' }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="fas fa-key me-1"></i>
                            <span class="permission-count" data-role-id="{{ $role->id }}">{{ count($role->permissions ?? []) }}</span> permissions
                        </span>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Permissions Matrix Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Permissions Matrix</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="expandAll()">
                        <i class="fas fa-expand-alt me-1"></i>Expand All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                        <i class="fas fa-compress-alt me-1"></i>Collapse All
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 matrix-table">
                    <thead>
                        <tr class="bg-light">
                            <th class="sticky-col" style="min-width: 250px;">Permission</th>
                            @foreach($roles as $role)
                            <th class="text-center" style="min-width: 120px;">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fw-bold">{{ $role->name }}</span>
                                    <small class="text-muted">{{ $role->slug }}</small>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionGroups as $groupName => $permissions)
                        <tr class="group-header" data-group="{{ Str::slug($groupName) }}">
                            <td colspan="{{ count($roles) + 1 }}" class="bg-light">
                                <button type="button" class="btn btn-link text-dark text-decoration-none p-0 group-toggle" onclick="toggleGroup('{{ Str::slug($groupName) }}')">
                                    <i class="fas fa-chevron-down me-2 group-icon"></i>
                                    <strong>{{ $groupName }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ count($permissions) }}</span>
                                </button>
                            </td>
                        </tr>
                        @foreach($permissions as $permissionKey => $permissionLabel)
                        <tr class="permission-row" data-group="{{ Str::slug($groupName) }}">
                            <td class="sticky-col">
                                <div class="d-flex align-items-center">
                                    <span class="permission-label">{{ $permissionLabel }}</span>
                                    <small class="text-muted ms-2">({{ $permissionKey }})</small>
                                </div>
                            </td>
                            @foreach($roles as $role)
                            @php
                                $hasPermission = in_array($permissionKey, $role->permissions ?? []);
                            @endphp
                            <td class="text-center permission-cell">
                                <div class="form-check d-flex justify-content-center">
                                    <input type="checkbox"
                                        class="form-check-input permission-checkbox"
                                        id="perm_{{ $role->id }}_{{ Str::slug($permissionKey) }}"
                                        data-role-id="{{ $role->id }}"
                                        data-permission="{{ $permissionKey }}"
                                        {{ $hasPermission ? 'checked' : '' }}>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.permission-indicator {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}

.permission-indicator.granted {
    background: linear-gradient(135deg, #10b981, #34d399);
}

.permission-indicator.denied {
    background: linear-gradient(135deg, #e5e7eb, #d1d5db);
}

.role-card {
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.role-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.matrix-table {
    font-size: 14px;
}

.matrix-table thead th {
    border-bottom: 2px solid var(--primary-color);
    vertical-align: middle;
}

.sticky-col {
    position: sticky;
    left: 0;
    background: white;
    z-index: 1;
    border-right: 2px solid #dee2e6;
}

.group-header td {
    font-weight: 600;
    cursor: pointer;
}

.group-header:hover td {
    background-color: #e9ecef !important;
}

.group-icon {
    transition: transform 0.2s ease;
    width: 16px;
}

.group-header.collapsed .group-icon {
    transform: rotate(-90deg);
}

.permission-row.hidden {
    display: none;
}

.permission-cell {
    transition: background-color 0.2s ease;
}

.permission-cell:hover {
    background-color: #f8f9fa;
}

.permission-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.permission-checkbox:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.permission-checkbox:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.permission-label {
    font-weight: 500;
}

/* Toast notification */
.toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.toast-item {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 12px 20px;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.3s ease;
}

.toast-item.success {
    border-left: 4px solid #10b981;
}

.toast-item.error {
    border-left: 4px solid #ef4444;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast container
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container';
    document.body.appendChild(toastContainer);

    // Handle permission checkbox changes
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roleId = this.dataset.roleId;
            const permission = this.dataset.permission;
            const enabled = this.checked;

            // Disable checkbox while updating
            this.disabled = true;

            fetch('{{ route("admin.roles.update-permission") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    role_id: roleId,
                    permission: permission,
                    enabled: enabled,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Permission updated successfully', 'success');
                    // Update permission count
                    const countEl = document.querySelector(`.permission-count[data-role-id="${roleId}"]`);
                    if (countEl && data.permissions) {
                        countEl.textContent = data.permissions.length;
                    }
                } else {
                    showToast(data.message || 'Failed to update permission', 'error');
                    this.checked = !enabled; // Revert
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to update permission', 'error');
                this.checked = !enabled; // Revert
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

function toggleGroup(groupName) {
    const header = document.querySelector(`.group-header[data-group="${groupName}"]`);
    const rows = document.querySelectorAll(`.permission-row[data-group="${groupName}"]`);

    header.classList.toggle('collapsed');

    rows.forEach(row => {
        row.classList.toggle('hidden');
    });
}

function expandAll() {
    document.querySelectorAll('.group-header').forEach(header => {
        header.classList.remove('collapsed');
    });
    document.querySelectorAll('.permission-row').forEach(row => {
        row.classList.remove('hidden');
    });
}

function collapseAll() {
    document.querySelectorAll('.group-header').forEach(header => {
        header.classList.add('collapsed');
    });
    document.querySelectorAll('.permission-row').forEach(row => {
        row.classList.add('hidden');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-item ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'}"></i>
        <span>${message}</span>
    `;

    document.querySelector('.toast-container').appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection
