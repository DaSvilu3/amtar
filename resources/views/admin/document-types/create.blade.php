@extends('layouts.admin')

@section('title', 'Create Document Type')

@section('content')
<div class="fade-in">
    <!-- Page Header -->
    <div class="page-title mb-4">
        <h1>Create Document Type</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.document-types.index') }}">Document Types</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.document-types.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Document Type Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="e.g., Civil ID, Trade License, Contract Agreement"
                               required
                               onkeyup="generateSlug()">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">A clear, descriptive name for the document type</small>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('slug') is-invalid @enderror"
                               id="slug"
                               name="slug"
                               value="{{ old('slug') }}"
                               placeholder="e.g., civil-id, trade-license"
                               required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">URL-friendly identifier (auto-generated from name)</small>
                    </div>

                    <div class="mb-3">
                        <label for="entity_type" class="form-label">Entity Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('entity_type') is-invalid @enderror"
                                id="entity_type"
                                name="entity_type"
                                required>
                            <option value="">Select Entity Type</option>
                            <option value="client" {{ old('entity_type') == 'client' ? 'selected' : '' }}>
                                Client Documents
                            </option>
                            <option value="project" {{ old('entity_type') == 'project' ? 'selected' : '' }}>
                                Project Documents
                            </option>
                            <option value="contract" {{ old('entity_type') == 'contract' ? 'selected' : '' }}>
                                Contract Documents
                            </option>
                        </select>
                        @error('entity_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Which entity this document type applies to</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Brief description of what this document is for...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Allowed File Types</label>
                        <div class="file-types-container">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="application/pdf" id="type_pdf" {{ in_array('application/pdf', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_pdf">
                                            <i class="fas fa-file-pdf text-danger me-1"></i>PDF Documents
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="application/msword" id="type_doc" {{ in_array('application/msword', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_doc">
                                            <i class="fas fa-file-word text-primary me-1"></i>Word Documents (.doc)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="application/vnd.openxmlformats-officedocument.wordprocessingml.document" id="type_docx" {{ in_array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_docx">
                                            <i class="fas fa-file-word text-primary me-1"></i>Word Documents (.docx)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="image/jpeg" id="type_jpeg" {{ in_array('image/jpeg', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_jpeg">
                                            <i class="fas fa-image text-success me-1"></i>JPEG Images
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="image/png" id="type_png" {{ in_array('image/png', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_png">
                                            <i class="fas fa-image text-info me-1"></i>PNG Images
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="application/vnd.ms-excel" id="type_xls" {{ in_array('application/vnd.ms-excel', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_xls">
                                            <i class="fas fa-file-excel text-success me-1"></i>Excel Files (.xls)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="file_types[]" value="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="type_xlsx" {{ in_array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', old('file_types', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="type_xlsx">
                                            <i class="fas fa-file-excel text-success me-1"></i>Excel Files (.xlsx)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Leave unchecked to allow all file types</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_required"
                                       name="is_required"
                                       value="1"
                                       {{ old('is_required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">
                                    <strong>Required Document</strong>
                                    <br>
                                    <small class="text-muted">Must be uploaded before proceeding</small>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                    <br>
                                    <small class="text-muted">Available for use in the system</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.document-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Document Type
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Information</h5>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Fields marked with * are required
                </p>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Slug is auto-generated but can be customized
                </p>
                <p class="text-muted small mb-2">
                    <i class="fas fa-check text-success me-2"></i>Required documents must be uploaded
                </p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-check text-success me-2"></i>File type restrictions are optional
                </p>
            </div>

            <div class="dashboard-card mt-3">
                <h5 class="mb-3"><i class="fas fa-lightbulb me-2"></i>Examples</h5>
                <div class="mb-3">
                    <strong class="text-primary">Client Documents:</strong>
                    <ul class="small text-muted mb-0 mt-1">
                        <li>Civil ID</li>
                        <li>Trade License</li>
                        <li>Passport Copy</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <strong class="text-primary">Project Documents:</strong>
                    <ul class="small text-muted mb-0 mt-1">
                        <li>Site Plan</li>
                        <li>Building Permit</li>
                        <li>Technical Drawings</li>
                    </ul>
                </div>
                <div>
                    <strong class="text-primary">Contract Documents:</strong>
                    <ul class="small text-muted mb-0 mt-1">
                        <li>Signed Contract</li>
                        <li>Payment Schedule</li>
                        <li>Terms & Conditions</li>
                    </ul>
                </div>
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
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .dashboard-card h5 {
        color: var(--primary-color);
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    function generateSlug() {
        const name = document.getElementById('name').value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        document.getElementById('slug').value = slug;
    }
</script>
@endpush
