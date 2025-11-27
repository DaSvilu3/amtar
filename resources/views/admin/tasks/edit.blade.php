@extends('layouts.admin')

@section('title', 'Edit Task')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit Task</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        Task Details
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $task->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                            data-services="{{ json_encode($project->services->load('service')) }}"
                                            data-milestones="{{ json_encode($project->milestones) }}"
                                            {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Linked Service</label>
                            <select name="project_service_id" id="project_service_id" class="form-select @error('project_service_id') is-invalid @enderror">
                                <option value="">No specific service</option>
                            </select>
                            @error('project_service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Milestone</label>
                            <select name="milestone_id" id="milestone_id" class="form-select @error('milestone_id') is-invalid @enderror">
                                <option value="">No milestone</option>
                            </select>
                            @error('milestone_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($availableTasks->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Dependencies (Tasks that must be completed first)</label>
                        <select name="dependencies[]" id="dependencies" class="form-select @error('dependencies') is-invalid @enderror" multiple size="5">
                            @foreach($availableTasks as $availableTask)
                                <option value="{{ $availableTask->id }}"
                                        {{ in_array($availableTask->id, old('dependencies', $task->dependencies->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $availableTask->title }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        @error('dependencies')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-cog me-2" style="color: var(--secondary-color);"></i>
                        Task Settings
                    </h5>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Progress</label>
                        <input type="range" name="progress" class="form-range" min="0" max="100"
                               value="{{ old('progress', $task->progress) }}" id="progressRange">
                        <div class="d-flex justify-content-between">
                            <span>0%</span>
                            <span id="progressValue">{{ old('progress', $task->progress) }}%</span>
                            <span>100%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                        @error('due_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Estimated Hours</label>
                            <input type="number" name="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror"
                                   value="{{ old('estimated_hours', $task->estimated_hours) }}" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Actual Hours</label>
                            <input type="number" name="actual_hours" class="form-control @error('actual_hours') is-invalid @enderror"
                                   value="{{ old('actual_hours', $task->actual_hours) }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Task
                    </button>
                    <a href="{{ route('admin.tasks.index', ['project_id' => $task->project_id]) }}" class="btn btn-secondary">
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

@push('scripts')
<script>
const currentProjectServiceId = {{ $task->project_service_id ?? 'null' }};
const currentMilestoneId = {{ $task->milestone_id ?? 'null' }};

document.getElementById('project_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const serviceSelect = document.getElementById('project_service_id');
    const milestoneSelect = document.getElementById('milestone_id');

    serviceSelect.innerHTML = '<option value="">No specific service</option>';
    milestoneSelect.innerHTML = '<option value="">No milestone</option>';

    if (option.value) {
        const services = JSON.parse(option.dataset.services || '[]');
        services.forEach(ps => {
            const opt = document.createElement('option');
            opt.value = ps.id;
            opt.textContent = ps.service ? ps.service.name : 'Service #' + ps.service_id;
            if (ps.id === currentProjectServiceId) opt.selected = true;
            serviceSelect.appendChild(opt);
        });

        const milestones = JSON.parse(option.dataset.milestones || '[]');
        milestones.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.title;
            if (m.id === currentMilestoneId) opt.selected = true;
            milestoneSelect.appendChild(opt);
        });
    }
});

// Progress range update
document.getElementById('progressRange').addEventListener('input', function() {
    document.getElementById('progressValue').textContent = this.value + '%';
});

// Trigger on page load
document.getElementById('project_id').dispatchEvent(new Event('change'));
</script>
@endpush
