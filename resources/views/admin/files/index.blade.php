@extends('layouts.admin')

@section('title', 'File Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>File Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Files</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.files.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Upload File
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
        <form action="{{ route('admin.files.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search files..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="document" {{ request('category') == 'document' ? 'selected' : '' }}>Documents</option>
                    <option value="image" {{ request('category') == 'image' ? 'selected' : '' }}>Images</option>
                    <option value="video" {{ request('category') == 'video' ? 'selected' : '' }}>Videos</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="jpg" {{ request('type') == 'jpg' ? 'selected' : '' }}>JPG</option>
                    <option value="png" {{ request('type') == 'png' ? 'selected' : '' }}>PNG</option>
                    <option value="doc" {{ request('type') == 'doc' ? 'selected' : '' }}>DOC</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Files Grid -->
    <div class="row">
        @forelse($files ?? [] as $file)
            <div class="col-md-3 mb-4">
                <div class="dashboard-card h-100">
                    <div class="file-preview mb-3 text-center">
                        @if(in_array($file->type ?? '', ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ $file->path }}" class="img-fluid rounded" alt="{{ $file->name }}">
                        @else
                            <i class="fas fa-file fa-5x text-muted"></i>
                        @endif
                    </div>
                    <h6 class="mb-2">{{ Str::limit($file->name ?? 'Untitled', 30) }}</h6>
                    <p class="small text-muted mb-2">
                        <span class="badge bg-info">{{ strtoupper($file->type ?? 'unknown') }}</span>
                        <span class="badge bg-secondary">{{ $file->size ?? 0 }} KB</span>
                    </p>
                    <p class="small text-muted mb-3">
                        <i class="fas fa-calendar me-1"></i>{{ $file->created_at ? $file->created_at->format('M d, Y') : '-' }}
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ $file->path }}" target="_blank" class="btn btn-sm btn-info flex-fill" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('admin.files.show', $file->id) }}" class="btn btn-sm btn-primary flex-fill" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $file->id }})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <form id="delete-form-{{ $file->id }}" action="{{ route('admin.files.destroy', $file->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="dashboard-card text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No files found</p>
                </div>
            </div>
        @endforelse
    </div>

    @if(isset($files) && $files->hasPages())
        <div class="mt-4">
            {{ $files->links() }}
        </div>
    @endif
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
                Are you sure you want to delete this file? This action cannot be undone.
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
    .file-preview {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    .file-preview img {
        max-height: 150px;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    let deleteFileId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    function confirmDelete(fileId) {
        deleteFileId = fileId;
        deleteModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteFileId) {
            document.getElementById('delete-form-' + deleteFileId).submit();
        }
    });
</script>
@endpush
