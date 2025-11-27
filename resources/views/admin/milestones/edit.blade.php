@extends('layouts.admin')

@section('title', 'Edit Milestone')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit Milestone</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.milestones.index') }}">Milestones</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.milestones.update', $milestone) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-flag me-2" style="color: var(--secondary-color);"></i>
                        Milestone Details
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $milestone->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4">{{ old('description', $milestone->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $milestone->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service Stage</label>
                            <select name="service_stage_id" class="form-select @error('service_stage_id') is-invalid @enderror">
                                <option value="">No specific stage</option>
                                @foreach($serviceStages as $stage)
                                    <option value="{{ $stage->id }}" {{ old('service_stage_id', $milestone->service_stage_id) == $stage->id ? 'selected' : '' }}>
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

            <div class="col-lg-4">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-cog me-2" style="color: var(--secondary-color);"></i>
                        Settings
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $milestone->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $milestone->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $milestone->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="overdue" {{ old('status', $milestone->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Target Date</label>
                        <input type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror"
                               value="{{ old('target_date', $milestone->target_date?->format('Y-m-d')) }}">
                        @error('target_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($milestone->completed_at)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Completed on {{ $milestone->completed_at->format('M d, Y') }}
                        </div>
                    @endif

                    <hr>

                    <h6 class="text-muted mb-3">Payment Information</h6>

                    <div class="mb-3">
                        <label class="form-label">Payment Percentage (%)</label>
                        <input type="number" name="payment_percentage" class="form-control @error('payment_percentage') is-invalid @enderror"
                               value="{{ old('payment_percentage', $milestone->payment_percentage) }}" min="0" max="100" step="0.01">
                        <small class="text-muted">Percentage of contract value due at this milestone</small>
                        @error('payment_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Amount</label>
                        <input type="number" name="payment_amount" class="form-control @error('payment_amount') is-invalid @enderror"
                               value="{{ old('payment_amount', $milestone->payment_amount) }}" min="0" step="0.01">
                        <small class="text-muted">Fixed amount (if not using percentage)</small>
                        @error('payment_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Milestone
                    </button>
                    <a href="{{ route('admin.milestones.index', ['project_id' => $milestone->project_id]) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
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
