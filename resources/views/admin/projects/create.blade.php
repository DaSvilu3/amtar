@extends('layouts.admin')

@section('title', 'Create Project')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create New Project</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="dashboard-card">
        <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
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

            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-file-upload me-2"></i>Required Documents</h5>

            <div class="mb-3">
                <label for="document_mulkiya" class="form-label">
                    Project Mulkiya (Property Title Deed) <span class="text-danger">*</span>
                </label>
                <input type="file" class="form-control @error('documents.project_mulkiya') is-invalid @enderror"
                       id="document_mulkiya" name="documents[project_mulkiya]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_mulkiya')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_kuroki" class="form-label">
                    Project Kuroki (Project Sketch/Plan) <span class="text-danger">*</span>
                </label>
                <input type="file" class="form-control @error('documents.project_kuroki') is-invalid @enderror"
                       id="document_kuroki" name="documents[project_kuroki]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_kuroki')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5 class="mb-3 mt-4"><i class="fas fa-file me-2"></i>Optional Documents</h5>

            <div class="mb-3">
                <label for="document_location_map" class="form-label">Location Map</label>
                <input type="file" class="form-control @error('documents.project_location_map') is-invalid @enderror"
                       id="document_location_map" name="documents[project_location_map]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_location_map')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_noc" class="form-label">NOC (No Objection Certificate)</label>
                <input type="file" class="form-control @error('documents.project_noc') is-invalid @enderror"
                       id="document_noc" name="documents[project_noc]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_noc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_municipality_approval" class="form-label">Municipality Approval</label>
                <input type="file" class="form-control @error('documents.project_municipality_approval') is-invalid @enderror"
                       id="document_municipality_approval" name="documents[project_municipality_approval]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_municipality_approval')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="document_building_permit" class="form-label">Building Permit</label>
                <input type="file" class="form-control @error('documents.project_building_permit') is-invalid @enderror"
                       id="document_building_permit" name="documents[project_building_permit]"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                @error('documents.project_building_permit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .form-label { font-weight: 500; color: var(--primary-color); }
</style>
@endpush
