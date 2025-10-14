@extends('layouts.admin')

@section('title', 'Edit Setting')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit Setting</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="dashboard-card">
                <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="key" class="form-label">Setting Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{ old('key', $setting->key) }}" required>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="group" class="form-label">Group <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('group') is-invalid @enderror" id="group" name="group" value="{{ old('group', $setting->group) }}" required>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="text" {{ old('type', $setting->type) == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="number" {{ old('type', $setting->type) == 'number' ? 'selected' : '' }}>Number</option>
                            <option value="boolean" {{ old('type', $setting->type) == 'boolean' ? 'selected' : '' }}>Boolean</option>
                            <option value="json" {{ old('type', $setting->type) == 'json' ? 'selected' : '' }}>JSON</option>
                            <option value="file" {{ old('type', $setting->type) == 'file' ? 'selected' : '' }}>File</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($setting->type == 'text' || !isset($setting->type))
                        <div class="mb-3">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $setting->value) }}">
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif($setting->type == 'number')
                        <div class="mb-3">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $setting->value) }}">
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif($setting->type == 'boolean')
                        <div class="mb-3">
                            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                            <select class="form-select" id="value" name="value">
                                <option value="1" {{ old('value', $setting->value) == '1' ? 'selected' : '' }}>True</option>
                                <option value="0" {{ old('value', $setting->value) == '0' ? 'selected' : '' }}>False</option>
                            </select>
                        </div>
                    @elseif($setting->type == 'json')
                        <div class="mb-3">
                            <label for="value" class="form-label">Value (JSON) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="value" name="value" rows="8">{{ old('value', $setting->value) }}</textarea>
                            <small class="text-muted">Enter valid JSON</small>
                        </div>
                    @elseif($setting->type == 'file')
                        <div class="mb-3">
                            <label class="form-label">Current File</label>
                            @if($setting->value)
                                <div class="mb-2">
                                    <a href="{{ $setting->value }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file me-1"></i>View Current File
                                    </a>
                                </div>
                            @endif
                            <label for="value_file" class="form-label">Upload New File</label>
                            <input type="file" class="form-control" id="value_file" name="value_file">
                            <small class="text-muted">Leave empty to keep current file</small>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $setting->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Timestamps</h5>
                <p class="text-muted small mb-2">
                    <strong>Created:</strong> {{ $setting->created_at ? $setting->created_at->format('M d, Y H:i') : '-' }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Updated:</strong> {{ $setting->updated_at ? $setting->updated_at->format('M d, Y H:i') : '-' }}
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
