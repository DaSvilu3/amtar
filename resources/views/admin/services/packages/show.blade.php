@extends('layouts.admin')

@section('title', 'Package Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Package Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.packages.index') }}">Packages</a></li>
                    <li class="breadcrumb-item active">{{ $package->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.services.packages.edit', $package) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.services.packages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 style="color: var(--primary-color);">{{ $package->name }}</h4>
                        <p class="text-muted mb-0">
                            <span class="badge bg-secondary me-1">{{ $package->mainService->name ?? '-' }}</span>
                            @if($package->subService)
                                <span class="badge bg-info">{{ $package->subService->name }}</span>
                            @endif
                        </p>
                    </div>
                    @if($package->is_active)
                        <span class="badge bg-success" style="font-size: 14px;">Active</span>
                    @else
                        <span class="badge bg-danger" style="font-size: 14px;">Inactive</span>
                    @endif
                </div>

                @if($package->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Description</h6>
                        <p>{{ $package->description }}</p>
                    </div>
                @endif

                <hr>

                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-cogs me-2" style="color: var(--secondary-color);"></i>
                    Included Services ({{ $package->services->count() }})
                </h5>

                @if($package->services->count() > 0)
                    @php
                        $groupedServices = $package->services->groupBy(fn($s) => $s->serviceStage->name ?? 'Other');
                    @endphp

                    @foreach($groupedServices as $stageName => $services)
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 text-muted">
                                <i class="fas fa-layer-group me-2"></i>
                                {{ $stageName }}
                                <span class="badge bg-light text-dark ms-2">{{ $services->count() }}</span>
                            </h6>
                            <div class="list-group list-group-flush">
                                @foreach($services as $service)
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $service->name }}
                                            @if($service->is_optional)
                                                <span class="badge bg-light text-muted ms-1">Optional</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('admin.services.services.edit', $service) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4 bg-light rounded">
                        <i class="fas fa-cogs fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No services assigned to this package</p>
                        <a href="{{ route('admin.services.packages.edit', $package) }}" class="btn btn-sm btn-primary mt-2">
                            Add Services
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                    Package Information
                </h5>

                <div class="mb-3">
                    <label class="text-muted small">Slug</label>
                    <p class="mb-0"><code>{{ $package->slug }}</code></p>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Sort Order</label>
                    <p class="mb-0">{{ $package->sort_order ?? 0 }}</p>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="text-muted small">Created At</label>
                    <p class="mb-0">{{ $package->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div class="mb-0">
                    <label class="text-muted small">Last Updated</label>
                    <p class="mb-0">{{ $package->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-bar me-2" style="color: var(--secondary-color);"></i>
                    Statistics
                </h5>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Services</span>
                    <span class="badge bg-primary">{{ $package->services->count() }}</span>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Required Services</span>
                    <span class="badge bg-secondary">{{ $package->services->where('is_optional', false)->count() }}</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span class="text-muted">Optional Services</span>
                    <span class="badge bg-info">{{ $package->services->where('is_optional', true)->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
</style>
@endpush
