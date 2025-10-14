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
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-project-diagram me-2"></i>Project Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $project->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Project Number:</strong></div>
                    <div class="col-md-9">{{ $project->project_number }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Client:</strong></div>
                    <div class="col-md-9">{{ $project->client->name ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Project Manager:</strong></div>
                    <div class="col-md-9">{{ $project->projectManager->name ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Progress:</strong></div>
                    <div class="col-md-9">{{ $project->progress ?? 0 }}%</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Budget:</strong></div>
                    <div class="col-md-9">{{ $project->budget ? number_format($project->budget, 3) . ' OMR' : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Location:</strong></div>
                    <div class="col-md-9">{{ $project->location ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Start Date:</strong></div>
                    <div class="col-md-9">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>End Date:</strong></div>
                    <div class="col-md-9">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $project->description ?? '-' }}</div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-cogs me-2"></i>Services</h5>

                @php
                    $projectServices = $project->services()->with('service.serviceStage')->orderBy('sort_order')->get();
                    $groupedServices = $projectServices->groupBy('serviceStage.name');
                @endphp

                @forelse($groupedServices as $stageName => $stageServices)
                    <div class="mb-3">
                        <h6 class="text-primary">{{ $stageName }}</h6>
                        <ul class="list-unstyled ms-3">
                            @foreach($stageServices as $projectService)
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    {{ $projectService->service->name }}
                                    <span class="badge bg-{{ $projectService->is_from_package ? 'info' : 'warning' }} ms-2">
                                        {{ $projectService->is_from_package ? 'Package' : 'Custom' }}
                                    </span>
                                    @if($projectService->is_completed)
                                        <span class="badge bg-success ms-2">Completed</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-muted">No services assigned to this project.</p>
                @endforelse
            </div>

            @if($project->contracts->count() > 0)
                <div class="dashboard-card">
                    <h5 class="mb-4"><i class="fas fa-file-contract me-2"></i>Contracts</h5>
                    <ul class="list-unstyled">
                        @foreach($project->contracts as $contract)
                            <li class="mb-2">
                                <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-decoration-none">
                                    <i class="fas fa-file-alt me-2"></i>{{ $contract->contract_number }} - {{ $contract->title }}
                                    @if($contract->auto_generated)
                                        <span class="badge bg-secondary ms-2">Auto-generated</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $project->created_at->format('M d, Y H:i:s') }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $project->updated_at->format('M d, Y H:i:s') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-card h5 { color: var(--primary-color); border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; }
</style>
@endpush
