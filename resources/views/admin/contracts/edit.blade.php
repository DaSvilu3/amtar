@extends('layouts.admin')

@section('title', 'Edit Contract')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit Contract</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">Contracts</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="dashboard-card">
        <form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contract_number" class="form-label">Contract Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contract_number') is-invalid @enderror"
                           id="contract_number" name="contract_number"
                           value="{{ old('contract_number', $contract->contract_number) }}"
                           required>
                    <small class="text-muted">Format: CNT-YYYY-XXXX</small>
                    @error('contract_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $contract->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                    <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id', $contract->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="project_id" class="form-label">Project (Optional)</label>
                    <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}"
                                {{ old('project_id', $contract->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3">{{ old('description', $contract->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="value" class="form-label">Contract Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror"
                           id="value" name="value" value="{{ old('value', $contract->value) }}" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                        <option value="OMR" {{ old('currency', $contract->currency) == 'OMR' ? 'selected' : '' }}>OMR</option>
                        <option value="USD" {{ old('currency', $contract->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency', $contract->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('currency', $contract->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="AED" {{ old('currency', $contract->currency) == 'AED' ? 'selected' : '' }}>AED</option>
                        <option value="SAR" {{ old('currency', $contract->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                    </select>
                    @error('currency')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                           id="start_date" name="start_date"
                           value="{{ old('start_date', $contract->start_date ? $contract->start_date->format('Y-m-d') : '') }}"
                           required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                           id="end_date" name="end_date"
                           value="{{ old('end_date', $contract->end_date ? $contract->end_date->format('Y-m-d') : '') }}"
                           required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="signed_date" class="form-label">Signed Date</label>
                    <input type="date" class="form-control @error('signed_date') is-invalid @enderror"
                           id="signed_date" name="signed_date"
                           value="{{ old('signed_date', $contract->signed_date ? $contract->signed_date->format('Y-m-d') : '') }}">
                    @error('signed_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="draft" {{ old('status', $contract->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ old('status', $contract->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="file" class="form-label">Contract File (PDF/DOC/DOCX)</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                           id="file" name="file" accept=".pdf,.doc,.docx">
                    <small class="text-muted">Maximum file size: 10MB</small>
                    @if($contract->file_path)
                        <div class="mt-2">
                            <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-download me-1"></i>View Current File
                            </a>
                        </div>
                    @endif
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="terms" class="form-label">Terms & Conditions</label>
                <textarea class="form-control @error('terms') is-invalid @enderror"
                          id="terms" name="terms" rows="5">{{ old('terms', $contract->terms) }}</textarea>
                @error('terms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Contract
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
