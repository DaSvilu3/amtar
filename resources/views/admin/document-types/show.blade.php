@extends('layouts.admin')

@section('title', 'View Document Type')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-title mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>{{ $documentType->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.document-types.index') }}">Document Types</a></li>
                        <li class="breadcrumb-item active">{{ $documentType->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.document-types.edit', $documentType) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-file-alt me-2"></i>Document Type Details</h5>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $documentType->name }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Slug</small>
                        <code>{{ $documentType->slug }}</code>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Entity Type</small>
                        <span class="badge bg-{{ $documentType->entity_type === 'client' ? 'primary' : ($documentType->entity_type === 'project' ? 'success' : 'warning') }}">
                            {{ ucfirst($documentType->entity_type) }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Required</small>
                        @if($documentType->is_required)
                            <span class="badge bg-danger"><i class="fas fa-asterisk me-1"></i>Required</span>
                        @else
                            <span class="badge bg-secondary">Optional</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Status</small>
                        @if($documentType->is_active)
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Inactive</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Files Count</small>
                        <strong>{{ $documentType->files ? $documentType->files->count() : 0 }}</strong> files
                    </div>
                </div>

                @if($documentType->description)
                    <div class="mb-3">
                        <small class="text-muted d-block">Description</small>
                        <p class="mb-0">{{ $documentType->description }}</p>
                    </div>
                @endif

                @if($documentType->file_types && count($documentType->file_types) > 0)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Allowed File Types</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($documentType->file_types as $fileType)
                                <span class="badge bg-light text-dark">
                                    @if(str_contains($fileType, 'pdf'))
                                        <i class="fas fa-file-pdf text-danger me-1"></i>PDF
                                    @elseif(str_contains($fileType, 'word') || str_contains($fileType, 'msword'))
                                        <i class="fas fa-file-word text-primary me-1"></i>Word
                                    @elseif(str_contains($fileType, 'image'))
                                        <i class="fas fa-image text-success me-1"></i>{{ str_contains($fileType, 'png') ? 'PNG' : 'JPEG' }}
                                    @elseif(str_contains($fileType, 'excel') || str_contains($fileType, 'spreadsheet'))
                                        <i class="fas fa-file-excel text-success me-1"></i>Excel
                                    @else
                                        {{ $fileType }}
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <small class="text-muted d-block">Allowed File Types</small>
                        <span class="text-muted">All file types allowed</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <div class="mb-3">
                    <small class="text-muted d-block">Created At</small>
                    <strong>{{ $documentType->created_at ? $documentType->created_at->format('M d, Y H:i') : 'N/A' }}</strong>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Last Updated</small>
                    <strong>{{ $documentType->updated_at ? $documentType->updated_at->format('M d, Y H:i') : 'N/A' }}</strong>
                </div>
            </div>

            <div class="dashboard-card mt-3">
                <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.document-types.edit', $documentType) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Document Type
                    </a>
                    <form action="{{ route('admin.document-types.destroy', $documentType) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this document type?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Document Type
                        </button>
                    </form>
                </div>
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
    .dashboard-card h5 {
        color: var(--primary-color);
        font-weight: 600;
    }
</style>
@endpush
