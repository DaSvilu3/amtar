@extends('layouts.admin')

@section('title', 'Create Task Template')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create Task Template</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.task-templates.index') }}">Task Templates</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.task-templates.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        Template Information
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Service <span class="text-danger">*</span></label>
                                <select name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Service Stage</label>
                                <select name="service_stage_id" class="form-select @error('service_stage_id') is-invalid @enderror">
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
                        </div>
                    </div>
                </div>

                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-user-cog me-2" style="color: var(--secondary-color);"></i>
                        Assignment Requirements
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Required Skills</label>
                        <select name="required_skills[]" class="form-select @error('required_skills') is-invalid @enderror" multiple size="5">
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ in_array($skill->id, old('required_skills', [])) ? 'selected' : '' }}>
                                    {{ $skill->name }} ({{ ucfirst($skill->category) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple skills</small>
                        @error('required_skills')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Required Expertise Level</label>
                        <select name="required_expertise_level" class="form-select @error('required_expertise_level') is-invalid @enderror">
                            <option value="">No minimum required</option>
                            <option value="junior" {{ old('required_expertise_level') == 'junior' ? 'selected' : '' }}>Junior</option>
                            <option value="mid" {{ old('required_expertise_level') == 'mid' ? 'selected' : '' }}>Mid-Level</option>
                            <option value="senior" {{ old('required_expertise_level') == 'senior' ? 'selected' : '' }}>Senior</option>
                            <option value="lead" {{ old('required_expertise_level') == 'lead' ? 'selected' : '' }}>Lead</option>
                        </select>
                        @error('required_expertise_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dependencies (Tasks that must complete first)</label>
                        <select name="dependencies[]" class="form-select @error('dependencies') is-invalid @enderror" multiple size="4">
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ in_array($template->id, old('dependencies', [])) ? 'selected' : '' }}>
                                    {{ $template->title }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        @error('dependencies')
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
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estimated Hours</label>
                        <input type="number" name="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror"
                               value="{{ old('estimated_hours') }}" min="1">
                        @error('estimated_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Default Duration (Days)</label>
                        <input type="number" name="default_duration_days" class="form-control @error('default_duration_days') is-invalid @enderror"
                               value="{{ old('default_duration_days') }}" min="1">
                        @error('default_duration_days')
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
                            <input type="checkbox" name="requires_review" class="form-check-input" id="requires_review"
                                   value="1" {{ old('requires_review') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_review">Requires Review</label>
                        </div>
                        <small class="text-muted">Task must be reviewed before completion</small>
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
                            <i class="fas fa-save me-2"></i>Create Template
                        </button>
                        <a href="{{ route('admin.task-templates.index') }}" class="btn btn-secondary">
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
