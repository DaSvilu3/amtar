@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Project Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.projects.edit', $item->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-project-diagram me-2"></i>Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $item->name ?? $item->title ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $item->description ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $item->created_at ? $item->created_at->format('M d, Y H:i:s') : '-' }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $item->updated_at ? $item->updated_at->format('M d, Y H:i:s') : '-' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-card h5 {{ color: var(--primary-color); border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; }}
</style>
@endpush
