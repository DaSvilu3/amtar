@extends('layouts.admin')

@section('title', 'Create Skill')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create Skill</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.skills.index') }}">Skills</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.skills.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        Skill Information
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Skill Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-cog me-2" style="color: var(--secondary-color);"></i>
                        Settings
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="domain" {{ old('category') == 'domain' ? 'selected' : '' }}>Domain</option>
                            <option value="soft_skill" {{ old('category') == 'soft_skill' ? 'selected' : '' }}>Soft Skill</option>
                            <option value="certification" {{ old('category') == 'certification' ? 'selected' : '' }}>Certification</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', 0) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Skill
                        </button>
                        <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
</style>
@endpush
