@extends('layouts.admin')

@section('title', 'Create Setting')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create New Setting</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="key" class="form-label">Setting Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{ old('key') }}" required>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use dot notation (e.g., app.name, mail.from)</small>
                    </div>

                    <div class="mb-3">
                        <label for="group" class="form-label">Group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('group') is-invalid @enderror" id="group" name="group" value="{{ old('group') }}" required>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">e.g., general, email, notifications</small>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                            <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                            <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="value-text">
                        <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}">
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 d-none" id="value-number">
                        <label for="value_number" class="form-label">Value (Number) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="value_number" name="value_number" value="{{ old('value_number') }}">
                    </div>

                    <div class="mb-3 d-none" id="value-boolean">
                        <label for="value_boolean" class="form-label">Value (Boolean) <span class="text-danger">*</span></label>
                        <select class="form-select" id="value_boolean" name="value_boolean">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="value-json">
                        <label for="value_json" class="form-label">Value (JSON) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="value_json" name="value_json" rows="6">{{ old('value_json', '{}') }}</textarea>
                        <small class="text-muted">Enter valid JSON</small>
                    </div>

                    <div class="mb-3 d-none" id="value-file">
                        <label for="value_file" class="form-label">Upload File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="value_file" name="value_file">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Setting Types</h5>
                <p class="text-muted small mb-2"><strong>Text:</strong> Simple string values</p>
                <p class="text-muted small mb-2"><strong>Number:</strong> Integer or decimal numbers</p>
                <p class="text-muted small mb-2"><strong>Boolean:</strong> True/False values</p>
                <p class="text-muted small mb-2"><strong>JSON:</strong> Complex structured data</p>
                <p class="text-muted small mb-0"><strong>File:</strong> Upload files (images, docs)</p>
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

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        // Hide all value fields
        document.querySelectorAll('[id^="value-"]').forEach(el => el.classList.add('d-none'));

        // Show relevant field
        const type = this.value;
        if (type) {
            document.getElementById('value-' + type).classList.remove('d-none');
        }
    });
</script>
@endpush
