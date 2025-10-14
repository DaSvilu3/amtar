@extends('layouts.admin')

@section('title', 'File Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>File Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.files.index') }}">Files</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ $file->path }}" target="_blank" class="btn btn-info">
                <i class="fas fa-download me-2"></i>Download
            </a>
            <a href="{{ route('admin.files.edit', $file->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-file me-2"></i>File Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $file->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Type:</strong></div>
                    <div class="col-md-9"><span class="badge bg-info">{{ strtoupper($file->type ?? 'unknown') }}</span></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Category:</strong></div>
                    <div class="col-md-9"><span class="badge bg-primary">{{ $file->category }}</span></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Size:</strong></div>
                    <div class="col-md-9">{{ $file->size }} KB</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $file->description ?? '-' }}</div>
                </div>

                @if(in_array($file->type ?? '', ['jpg', 'jpeg', 'png', 'gif']))
                    <div class="mt-4">
                        <h6>Preview:</h6>
                        <img src="{{ $file->path }}" class="img-fluid rounded" alt="{{ $file->name }}">
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2"><strong>Uploaded:</strong> {{ $file->created_at->format('M d, Y H:i:s') }}</p>
                <p class="text-muted small mb-0"><strong>Updated:</strong> {{ $file->updated_at->format('M d, Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
