@extends('layouts.admin')

@section('title', 'Edit Service Package')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h1>Edit Service Package</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.services.packages.index') }}">Packages</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.services.packages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <form action="{{ route('admin.services.packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <h5 class="mb-4" style="color: var(--primary-color);">Package Information</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="main_service_id" class="form-label">Main Service <span class="text-danger">*</span></label>
                            <select class="form-select @error('main_service_id') is-invalid @enderror"
                                    id="main_service_id" name="main_service_id" required>
                                <option value="">Select Main Service</option>
                                @foreach($mainServices as $main)
                                    <option value="{{ $main->id }}" {{ old('main_service_id', $package->main_service_id) == $main->id ? 'selected' : '' }}>
                                        {{ $main->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('main_service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sub_service_id" class="form-label">Sub Service</label>
                            <select class="form-select @error('sub_service_id') is-invalid @enderror"
                                    id="sub_service_id" name="sub_service_id">
                                <option value="">None (Optional)</option>
                                @foreach($subServices as $sub)
                                    <option value="{{ $sub->id }}" data-main="{{ $sub->main_service_id }}"
                                            {{ old('sub_service_id', $package->sub_service_id) == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Package Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $package->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services Selection -->
                <div class="dashboard-card">
                    <h5 class="mb-4" style="color: var(--primary-color);">
                        <i class="fas fa-cogs me-2" style="color: var(--secondary-color);"></i>
                        Package Services
                    </h5>
                    <p class="text-muted mb-4">Select the services that should be included in this package.</p>

                    @php
                        $packageServiceIds = old('services', $package->services->pluck('id')->toArray());
                    @endphp

                    @foreach($stages as $stage)
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">
                                <i class="fas fa-layer-group me-2 text-muted"></i>
                                {{ $stage->name }}
                            </h6>
                            <div class="row">
                                @foreach($services->where('service_stage_id', $stage->id) as $service)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="services[]" value="{{ $service->id }}"
                                                   id="service_{{ $service->id }}"
                                                   {{ in_array($service->id, $packageServiceIds) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service_{{ $service->id }}">
                                                {{ $service->name }}
                                                @if($service->is_optional)
                                                    <span class="badge bg-light text-muted">Optional</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($services->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-cogs fa-2x mb-2"></i>
                            <p class="mb-0">No services available. Create services first.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card sticky-top mb-4" style="top: 20px;">
                    <h5 class="mb-4" style="color: var(--primary-color);">Actions</h5>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Package
                        </button>
                        <a href="{{ route('admin.services.packages.show', $package) }}" class="btn btn-info">
                            <i class="fas fa-eye me-2"></i>View Package
                        </a>
                        <a href="{{ route('admin.services.packages.index') }}" class="btn btn-light">
                            Cancel
                        </a>
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <small>
                            <strong>Slug:</strong> {{ $package->slug }}<br>
                            <strong>Created:</strong> {{ $package->created_at->format('M d, Y') }}<br>
                            <strong>Updated:</strong> {{ $package->updated_at->format('M d, Y') }}
                        </small>
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

@push('scripts')
<script>
    // Filter sub-services based on main service selection
    document.getElementById('main_service_id').addEventListener('change', function() {
        const mainId = this.value;
        const subSelect = document.getElementById('sub_service_id');
        const options = subSelect.querySelectorAll('option[data-main]');

        options.forEach(option => {
            if (mainId === '' || option.dataset.main === mainId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });

        // Reset sub service if not matching
        if (subSelect.selectedOptions[0]?.dataset.main && subSelect.selectedOptions[0].dataset.main !== mainId) {
            subSelect.value = '';
        }
    });

    // Trigger on page load
    document.getElementById('main_service_id').dispatchEvent(new Event('change'));
</script>
@endpush
