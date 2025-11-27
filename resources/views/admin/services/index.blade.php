@extends('layouts.admin')

@section('title', 'Service Management')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Service Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Services</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="dashboard-card text-center h-100">
                <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-building text-white" style="font-size: 20px;"></i>
                </div>
                <h3 class="mb-1" style="color: var(--primary-color);">{{ $stats['main_services'] }}</h3>
                <p class="text-muted mb-0">Main Services</p>
                <a href="{{ route('admin.services.main.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="dashboard-card text-center h-100">
                <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-code-branch text-white" style="font-size: 20px;"></i>
                </div>
                <h3 class="mb-1" style="color: var(--primary-color);">{{ $stats['sub_services'] }}</h3>
                <p class="text-muted mb-0">Sub Services</p>
                <a href="{{ route('admin.services.sub.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="dashboard-card text-center h-100">
                <div style="background: linear-gradient(135deg, #f093fb, #f5576c); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-box text-white" style="font-size: 20px;"></i>
                </div>
                <h3 class="mb-1" style="color: var(--primary-color);">{{ $stats['packages'] }}</h3>
                <p class="text-muted mb-0">Packages</p>
                <a href="{{ route('admin.services.packages.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="dashboard-card text-center h-100">
                <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-layer-group text-white" style="font-size: 20px;"></i>
                </div>
                <h3 class="mb-1" style="color: var(--primary-color);">{{ $stats['stages'] }}</h3>
                <p class="text-muted mb-0">Stages</p>
                <a href="{{ route('admin.services.stages.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="dashboard-card text-center h-100">
                <div style="background: linear-gradient(135deg, #11998e, #38ef7d); width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i class="fas fa-cogs text-white" style="font-size: 20px;"></i>
                </div>
                <h3 class="mb-1" style="color: var(--primary-color);">{{ $stats['services'] }}</h3>
                <p class="text-muted mb-0">Services</p>
                <a href="{{ route('admin.services.services.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Service Hierarchy -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-sitemap me-2" style="color: var(--secondary-color);"></i>
                    Service Hierarchy
                </h5>

                <div class="accordion" id="serviceHierarchy">
                    @foreach($mainServices as $index => $mainService)
                        <div class="accordion-item border-0 mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#main-{{ $mainService->id }}"
                                        style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                                    <span class="me-3">
                                        <i class="fas fa-building" style="color: var(--secondary-color);"></i>
                                    </span>
                                    <strong>{{ $mainService->name }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ $mainService->sub_services_count }} sub</span>
                                    <span class="badge bg-info ms-1">{{ $mainService->service_packages_count }} packages</span>
                                    @if(!$mainService->is_active)
                                        <span class="badge bg-danger ms-2">Inactive</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="main-{{ $mainService->id }}"
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                 data-bs-parent="#serviceHierarchy">
                                <div class="accordion-body">
                                    @if($mainService->subServices->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @foreach($mainService->subServices as $subService)
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <div>
                                                        <i class="fas fa-code-branch text-muted me-2"></i>
                                                        {{ $subService->name }}
                                                        @if(!$subService->is_active)
                                                            <span class="badge bg-danger ms-2">Inactive</span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('admin.services.sub.edit', $subService) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No sub-services defined</p>
                                    @endif

                                    <div class="mt-3">
                                        <a href="{{ route('admin.services.sub.create') }}?main_service_id={{ $mainService->id }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-plus me-1"></i>Add Sub Service
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($mainServices->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No main services defined yet</p>
                        <a href="{{ route('admin.services.main.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create First Main Service
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Packages & Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-bolt me-2" style="color: var(--secondary-color);"></i>
                    Quick Actions
                </h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.services.main.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-building me-2"></i>Add Main Service
                    </a>
                    <a href="{{ route('admin.services.sub.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-code-branch me-2"></i>Add Sub Service
                    </a>
                    <a href="{{ route('admin.services.packages.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-box me-2"></i>Add Service Package
                    </a>
                    <a href="{{ route('admin.services.stages.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-layer-group me-2"></i>Add Service Stage
                    </a>
                    <a href="{{ route('admin.services.services.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cogs me-2"></i>Add Service
                    </a>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-clock me-2" style="color: var(--secondary-color);"></i>
                    Recent Packages
                </h5>

                @forelse($recentPackages as $package)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #f093fb, #f5576c); display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                            <i class="fas fa-box text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ Str::limit($package->name, 25) }}</h6>
                            <small class="text-muted">{{ $package->mainService->name ?? '-' }}</small>
                        </div>
                        <a href="{{ route('admin.services.packages.edit', $package) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No packages created yet</p>
                @endforelse

                @if($recentPackages->isNotEmpty())
                    <a href="{{ route('admin.services.packages.index') }}" class="btn btn-link w-100 text-center">
                        View All Packages <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .btn-outline-primary { color: var(--primary-color); border-color: var(--primary-color); }
    .btn-outline-primary:hover { background-color: var(--primary-color); color: white; }
    .accordion-button:not(.collapsed) { background: rgba(243, 200, 135, 0.2) !important; }
    .accordion-button:focus { box-shadow: none; }
</style>
@endpush
