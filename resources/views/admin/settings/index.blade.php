@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>System Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.settings.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Setting
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter -->
    <div class="dashboard-card mb-4">
        <form action="{{ route('admin.settings.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search settings..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="group" class="form-select">
                    <option value="">All Groups</option>
                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                            {{ ucfirst($group) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Text</option>
                    <option value="number" {{ request('type') == 'number' ? 'selected' : '' }}>Number</option>
                    <option value="boolean" {{ request('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                    <option value="json" {{ request('type') == 'json' ? 'selected' : '' }}>JSON</option>
                    <option value="file" {{ request('type') == 'file' ? 'selected' : '' }}>File</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Settings Grouped -->
    @foreach($settingsByGroup ?? [] as $groupName => $groupSettings)
        <div class="dashboard-card mb-4">
            <h4 class="mb-4">
                <i class="fas fa-layer-group me-2"></i>{{ ucfirst($groupName) }}
            </h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupSettings as $setting)
                            <tr>
                                <td><code>{{ $setting->key }}</code></td>
                                <td>
                                    @if($setting->type == 'boolean')
                                        <span class="badge {{ $setting->value ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $setting->value ? 'Yes' : 'No' }}
                                        </span>
                                    @elseif($setting->type == 'file')
                                        @if($setting->value)
                                            <a href="{{ $setting->value }}" target="_blank">
                                                <i class="fas fa-file me-1"></i>View File
                                            </a>
                                        @else
                                            -
                                        @endif
                                    @elseif($setting->type == 'json')
                                        <button class="btn btn-sm btn-info" onclick="showJson({{ $setting->id }})">
                                            <i class="fas fa-code"></i> View JSON
                                        </button>
                                    @else
                                        {{ Str::limit($setting->value ?? '-', 50) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $setting->type }}</span>
                                </td>
                                <td>{{ $setting->description ?? '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.settings.edit', $setting->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $setting->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $setting->id }}" action="{{ route('admin.settings.destroy', $setting->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if(empty($settingsByGroup))
        <div class="dashboard-card text-center py-5">
            <i class="fas fa-cog fa-3x text-muted mb-3"></i>
            <p class="text-muted">No settings found</p>
        </div>
    @endif
</div>

<!-- JSON View Modal -->
<div class="modal fade" id="jsonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">JSON Value</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="bg-light p-3 rounded"></pre>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this setting? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }
    .table thead {
        background-color: var(--primary-color);
        color: white;
    }
    .dashboard-card h4 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
    }
</style>
@endpush

@push('scripts')
<script>
    let deleteSettingId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const jsonModal = new bootstrap.Modal(document.getElementById('jsonModal'));

    function confirmDelete(settingId) {
        deleteSettingId = settingId;
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteSettingId) {
            document.getElementById('delete-form-' + deleteSettingId).submit();
        }
    });

    function showJson(settingId) {
        // This would need to fetch the actual JSON value from the server
        // For now, showing a placeholder
        document.getElementById('jsonContent').textContent = 'JSON content for setting ' + settingId;
        jsonModal.show();
    }
</script>
@endpush
