@extends('layouts.admin')

@section('title', 'Create Client')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create New Client</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}">
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="prospect" {{ old('status') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="relationship_manager_id" class="form-label">
                                <i class="fas fa-user-tie me-1"></i>Relationship Manager
                            </label>
                            <select class="form-select @error('relationship_manager_id') is-invalid @enderror" id="relationship_manager_id" name="relationship_manager_id">
                                <option value="">-- Select Employee --</option>
                                @foreach($employees ?? [] as $employee)
                                    <option value="{{ $employee->id }}" {{ old('relationship_manager_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">The employee responsible for this client relationship</small>
                            @error('relationship_manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3"><i class="fas fa-file-upload me-2"></i>Required Documents</h5>

                    <div class="mb-3">
                        <label for="document_civil_id" class="form-label">
                            Client ID / Civil ID <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control @error('documents.client_civil_id') is-invalid @enderror"
                               id="document_civil_id" name="documents[client_civil_id]"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                        @error('documents.client_civil_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="document_commercial_registration" class="form-label">
                            Commercial Registration <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control @error('documents.client_commercial_registration') is-invalid @enderror"
                               id="document_commercial_registration" name="documents[client_commercial_registration]"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                        @error('documents.client_commercial_registration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="mb-3 mt-4"><i class="fas fa-file me-2"></i>Optional Documents</h5>

                    <div class="mb-3">
                        <label for="document_tax_certificate" class="form-label">Tax Registration Certificate</label>
                        <input type="file" class="form-control @error('documents.client_tax_certificate') is-invalid @enderror"
                               id="document_tax_certificate" name="documents[client_tax_certificate]"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                        @error('documents.client_tax_certificate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="document_authorization_letter" class="form-label">Authorization Letter</label>
                        <input type="file" class="form-control @error('documents.client_authorization_letter') is-invalid @enderror"
                               id="document_authorization_letter" name="documents[client_authorization_letter]"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG</small>
                        @error('documents.client_authorization_letter')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Client
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Required fields marked with *
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-check text-success me-2"></i>Email must be unique
                </p>
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
    .btn-primary:hover {
        background-color: var(--hover-color);
        border-color: var(--hover-color);
    }
    .form-label {
        font-weight: 500;
        color: var(--primary-color);
    }
</style>
@endpush
