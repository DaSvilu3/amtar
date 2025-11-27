@extends('layouts.admin')

@section('title', 'Create Service')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Create Service</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <div class="dashboard-card">
        <form action="{{ route('admin.services.services.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="service_stage_id" class="form-label">Service Stage <span class="text-danger">*</span></label>
                        <select class="form-select @error('service_stage_id') is-invalid @enderror"
                                id="service_stage_id" name="service_stage_id" required>
                            <option value="">Select Stage</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" {{ old('service_stage_id') == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_stage_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., Site Survey, Floor Plan Design">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Brief description of this service">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Documents</label>
                        <div id="documents-container">
                            @if(old('required_documents'))
                                @foreach(old('required_documents') as $index => $doc)
                                    <div class="input-group mb-2 document-row">
                                        <input type="text" class="form-control" name="required_documents[]"
                                               value="{{ $doc }}" placeholder="Document name">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeDocument(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocument()">
                            <i class="fas fa-plus me-1"></i>Add Document
                        </button>
                        <small class="text-muted d-block mt-1">List of documents required for this service</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_optional" name="is_optional" value="1"
                                   {{ old('is_optional') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_optional">Optional Service</label>
                        </div>
                        <small class="text-muted">Optional services can be skipped in projects</small>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.services.services.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Service
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
</style>
@endpush

@push('scripts')
<script>
    function addDocument() {
        const container = document.getElementById('documents-container');
        const row = document.createElement('div');
        row.className = 'input-group mb-2 document-row';
        row.innerHTML = `
            <input type="text" class="form-control" name="required_documents[]" placeholder="Document name">
            <button type="button" class="btn btn-outline-danger" onclick="removeDocument(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(row);
    }

    function removeDocument(btn) {
        btn.closest('.document-row').remove();
    }
</script>
@endpush
