@extends('layouts.admin')

@section('title', 'Upload File')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Upload New File</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.files.index') }}">Files</a></li>
                <li class="breadcrumb-item active">Upload</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.files.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">File Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                            <option value="document" {{ old('category') == 'document' ? 'selected' : '' }}>Document</option>
                            <option value="image" {{ old('category') == 'image' ? 'selected' : '' }}>Image</option>
                            <option value="video" {{ old('category') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload File
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Allowed File Types</h5>
                <p class="text-muted small mb-2">Documents: PDF, DOC, DOCX, XLS, XLSX</p>
                <p class="text-muted small mb-2">Images: JPG, PNG, GIF, SVG</p>
                <p class="text-muted small mb-2">Videos: MP4, AVI, MOV</p>
                <p class="text-muted small mb-0">Max file size: 10MB</p>
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
    .form-label {
        font-weight: 500;
        color: var(--primary-color);
    }
</style>
@endpush
