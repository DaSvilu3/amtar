@extends('layouts.admin')

@section('title', 'Document Types')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Document Types</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Document Types</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.document-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Document Type
        </a>
    </div>

    <!-- Document Types by Entity Type -->
    @foreach(['client' => 'Client Documents', 'project' => 'Project Documents', 'contract' => 'Contract Documents'] as $entityType => $title)
        <div class="dashboard-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fas fa-{{ $entityType == 'client' ? 'user' : ($entityType == 'project' ? 'project-diagram' : 'file-contract') }} me-2"></i>
                    {{ $title }}
                </h5>
                <span class="badge bg-primary">
                    {{ isset($documentTypes[$entityType]) ? $documentTypes[$entityType]->count() : 0 }} types
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 30px;">ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th style="width: 100px;">Required</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 200px;">File Types</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentTypes[$entityType] ?? [] as $documentType)
                            <tr>
                                <td>{{ $documentType->id }}</td>
                                <td>
                                    <strong>{{ $documentType->name }}</strong>
                                    @if($documentType->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($documentType->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <code class="text-muted">{{ $documentType->slug }}</code>
                                </td>
                                <td>
                                    @if($documentType->is_required)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-exclamation-circle me-1"></i>Required
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Optional</span>
                                    @endif
                                </td>
                                <td>
                                    @if($documentType->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-pause-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($documentType->file_types && count($documentType->file_types) > 0)
                                        <small class="text-muted">
                                            {{ implode(', ', array_slice($documentType->file_types, 0, 2)) }}
                                            @if(count($documentType->file_types) > 2)
                                                <span class="badge bg-info">+{{ count($documentType->file_types) - 2 }}</span>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">All types</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.document-types.edit', $documentType->id) }}"
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                onclick="confirmDelete({{ $documentType->id }})"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $documentType->id }}"
                                          action="{{ route('admin.document-types.destroy', $documentType->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No document types found for {{ strtolower($title) }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
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
                Are you sure you want to delete this document type? This action cannot be undone.
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
    .dashboard-card h5 {
        color: var(--primary-color);
        font-weight: 600;
    }
    code {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
    let deleteDocumentTypeId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    function confirmDelete(documentTypeId) {
        deleteDocumentTypeId = documentTypeId;
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteDocumentTypeId) {
            document.getElementById('delete-form-' + deleteDocumentTypeId).submit();
        }
    });
</script>
@endpush
