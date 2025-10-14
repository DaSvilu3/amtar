@extends('layouts.admin')

@section('title', 'Setting Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Setting Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.settings.edit', $setting->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Setting
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-cog me-2"></i>Setting Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Key:</strong></div>
                    <div class="col-md-9"><code>{{ $setting->key }}</code></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Group:</strong></div>
                    <div class="col-md-9"><span class="badge bg-primary">{{ $setting->group }}</span></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Type:</strong></div>
                    <div class="col-md-9"><span class="badge bg-info">{{ $setting->type }}</span></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Value:</strong></div>
                    <div class="col-md-9">
                        @if($setting->type == 'boolean')
                            <span class="badge {{ $setting->value ? 'bg-success' : 'bg-secondary' }}">
                                {{ $setting->value ? 'True' : 'False' }}
                            </span>
                        @elseif($setting->type == 'file')
                            @if($setting->value)
                                <a href="{{ $setting->value }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file me-1"></i>View File
                                </a>
                            @else
                                -
                            @endif
                        @elseif($setting->type == 'json')
                            <pre class="bg-light p-3 rounded"><code>{{ $setting->value }}</code></pre>
                        @else
                            {{ $setting->value ?? '-' }}
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $setting->description ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <div class="mb-3">
                    <small class="text-muted">Created At:</small>
                    <p class="mb-0">{{ $setting->created_at ? $setting->created_at->format('M d, Y H:i:s') : '-' }}</p>
                </div>
                <div>
                    <small class="text-muted">Updated At:</small>
                    <p class="mb-0">{{ $setting->updated_at ? $setting->updated_at->format('M d, Y H:i:s') : '-' }}</p>
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
    .dashboard-card h5 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
    }
</style>
@endpush
