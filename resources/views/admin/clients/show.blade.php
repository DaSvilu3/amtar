@extends('layouts.admin')

@section('title', 'Client Details')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Client Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Client
            </a>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-user me-2"></i>Client Information</h5>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $client->name }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Email:</strong></div>
                    <div class="col-md-9"><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Phone:</strong></div>
                    <div class="col-md-9"><a href="tel:{{ $client->phone }}">{{ $client->phone }}</a></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Company:</strong></div>
                    <div class="col-md-9">{{ $client->company ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Address:</strong></div>
                    <div class="col-md-9">{{ $client->address ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>City:</strong></div>
                    <div class="col-md-9">{{ $client->city ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Country:</strong></div>
                    <div class="col-md-9">{{ $client->country ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        @php
                            $statusColors = [
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'prospect' => 'info',
                                'archived' => 'dark',
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$client->status] ?? 'secondary' }}">{{ ucfirst($client->status) }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3"><strong>Relationship Manager:</strong></div>
                    <div class="col-md-9">
                        @if($client->relationshipManager)
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                    {{ strtoupper(substr($client->relationshipManager->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong>{{ $client->relationshipManager->name }}</strong>
                                    <br><small class="text-muted">{{ $client->relationshipManager->email }}</small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">No relationship manager assigned</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <h5 class="mb-4"><i class="fas fa-file-alt me-2"></i>Documents</h5>
                @forelse($client->files ?? [] as $file)
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                        <div>
                            <i class="fas fa-{{ str_contains($file->mime_type ?? '', 'pdf') ? 'file-pdf text-danger' : 'file-image text-primary' }} me-2"></i>
                            <strong>{{ $file->documentType->name ?? $file->name }}</strong><br>
                            <small class="text-muted">
                                {{ $file->original_name }} - {{ number_format(($file->file_size ?? 0) / 1024, 1) }} KB
                                @if($file->created_at)
                                    - Uploaded {{ $file->created_at->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                        <div>
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ Storage::url($file->file_path) }}" download class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No documents uploaded yet</p>
                @endforelse
            </div>

            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-project-diagram me-2"></i>Projects</h5>
                @forelse($client->projects ?? [] as $project)
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-2">
                        <div>
                            <strong>{{ $project->name }}</strong><br>
                            <small class="text-muted">{{ $project->status }}</small>
                        </div>
                        <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                            View
                        </a>
                    </div>
                @empty
                    <p class="text-muted">No projects found</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card mb-3">
                <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                <div class="mb-3">
                    <small class="text-muted">Total Projects:</small>
                    <p class="mb-0 h4">{{ $client->projects_count ?? 0 }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Active Projects:</small>
                    <p class="mb-0 h4">{{ $client->active_projects_count ?? 0 }}</p>
                </div>
                <div>
                    <small class="text-muted">Total Value:</small>
                    <p class="mb-0 h4">${{ number_format($client->total_value ?? 0, 2) }}</p>
                </div>
            </div>

            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <div class="mb-3">
                    <small class="text-muted">Created At:</small>
                    <p class="mb-0">{{ $client->created_at->format('M d, Y H:i:s') }}</p>
                </div>
                <div>
                    <small class="text-muted">Updated At:</small>
                    <p class="mb-0">{{ $client->updated_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-card h5 {
        color: var(--primary-color);
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 10px;
    }
</style>
@endpush
