@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit Client</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Client Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $client->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $client->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company', $client->company) }}">
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $client->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $client->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $client->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="prospect" {{ old('status', $client->status) == 'prospect' ? 'selected' : '' }}>Prospect</option>
                                <option value="archived" {{ old('status', $client->status) == 'archived' ? 'selected' : '' }}>Archived</option>
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
                                    <option value="{{ $employee->id }}" {{ old('relationship_manager_id', $client->relationship_manager_id) == $employee->id ? 'selected' : '' }}>
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

                    <h5 class="mb-3"><i class="fas fa-file-upload me-2"></i>Documents</h5>
                    <p class="text-muted small mb-3">Upload new documents or replace existing ones</p>

                    @foreach($documentTypes ?? [] as $docType)
                    @php
                        $existingFile = $client->files->firstWhere('document_type_id', $docType->id);
                    @endphp
                    <div class="mb-3">
                        <label for="document_{{ $docType->slug }}" class="form-label">
                            {{ $docType->name }}
                            @if($docType->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if($existingFile)
                        <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-file me-2"></i>
                                <strong>Current:</strong> {{ $existingFile->original_name }}
                                <span class="text-muted small ms-2">({{ number_format($existingFile->file_size / 1024, 1) }} KB)</span>
                            </div>
                            <a href="{{ Storage::url($existingFile->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                        @endif

                        <input type="file" class="form-control @error('documents.' . $docType->slug) is-invalid @enderror"
                               id="document_{{ $docType->slug }}" name="documents[{{ $docType->slug }}]"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">
                            Accepted formats: PDF, JPG, PNG
                            @if($existingFile)
                                - Upload a new file to replace the existing one
                            @endif
                        </small>
                        @error('documents.' . $docType->slug)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Client
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $client->created_at->format('M d, Y H:i') }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $client->updated_at->format('M d, Y H:i') }}
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
