@extends('layouts.admin')

@section('title', 'Contract Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Contract Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.contracts.print', $contract->id) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print me-2"></i>Print Contract
            </a>
            <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-file-contract me-2"></i>Contract Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Contract Number:</strong></div>
                    <div class="col-md-9">{{ $contract->contract_number }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Title:</strong></div>
                    <div class="col-md-9">{{ $contract->title }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Client:</strong></div>
                    <div class="col-md-9">{{ $contract->client->name ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Project:</strong></div>
                    <div class="col-md-9">
                        @if($contract->project)
                            <a href="{{ route('admin.projects.show', $contract->project->id) }}">{{ $contract->project->name }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        <span class="badge bg-{{ $contract->status === 'active' ? 'success' : ($contract->status === 'draft' ? 'secondary' : ($contract->status === 'pending' ? 'warning' : 'danger')) }}">
                            {{ ucfirst($contract->status) }}
                        </span>
                        @if($contract->auto_generated)
                            <span class="badge bg-info ms-2">Auto-generated</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Value:</strong></div>
                    <div class="col-md-9">{{ $contract->value ? number_format($contract->value, 3) . ' ' . ($contract->currency ?? 'OMR') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Start Date:</strong></div>
                    <div class="col-md-9">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>End Date:</strong></div>
                    <div class="col-md-9">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Signed Date:</strong></div>
                    <div class="col-md-9">{{ $contract->signed_date ? \Carbon\Carbon::parse($contract->signed_date)->format('M d, Y') : '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $contract->description ?? '-' }}</div>
                </div>

                @if($contract->terms)
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Terms:</strong></div>
                        <div class="col-md-9">{{ $contract->terms }}</div>
                    </div>
                @endif

                @if($contract->file_path)
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>File:</strong></div>
                        <div class="col-md-9">
                            <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download Contract
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($contract->services && count($contract->services) > 0)
                <div class="dashboard-card">
                    <h5 class="mb-4"><i class="fas fa-cogs me-2"></i>Services</h5>

                    @foreach($contract->services as $stageName => $serviceNames)
                        <div class="mb-3">
                            <h6 class="text-primary">{{ $stageName }}</h6>
                            <ul class="list-unstyled ms-3">
                                @foreach($serviceNames as $serviceName)
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>{{ $serviceName }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-4">
                <h5 class="mb-3"><i class="fas fa-user me-2"></i>Created By</h5>
                <p class="mb-0">{{ $contract->creator->name ?? '-' }}</p>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $contract->created_at->format('M d, Y H:i:s') }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $contract->updated_at->format('M d, Y H:i:s') }}
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
