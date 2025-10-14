@extends('layouts.admin')

@section('title', 'Create Project')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Create New Project</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Error!</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information -->
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="project_number" class="form-label">Project Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('project_number') is-invalid @enderror"
                           id="project_number" name="project_number"
                           value="{{ old('project_number', 'PRJ-' . date('Y') . '-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT)) }}" required>
                    @error('project_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                    <select class="form-select @error('client_id') is-invalid @enderror"
                            id="client_id" name="client_id" required>
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} - {{ $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="project_manager_id" class="form-label">Project Manager</label>
                    <select class="form-select @error('project_manager_id') is-invalid @enderror"
                            id="project_manager_id" name="project_manager_id">
                        <option value="">Select Project Manager</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('project_manager_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                        <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="budget" class="form-label">Budget (OMR)</label>
                    <input type="number" class="form-control @error('budget') is-invalid @enderror"
                           id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0">
                    @error('budget')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                           id="location" name="location" value="{{ old('location') }}">
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                           id="start_date" name="start_date" value="{{ old('start_date') }}">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="progress" class="form-label">Progress (%)</label>
                    <input type="number" class="form-control @error('progress') is-invalid @enderror"
                           id="progress" name="progress" value="{{ old('progress', 0) }}" min="0" max="100">
                    @error('progress')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Service Selection -->
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-cogs me-2"></i>Service Selection <span class="text-danger">*</span></h5>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="main_service_id" class="form-label">Main Service <span class="text-danger">*</span></label>
                    <select class="form-select @error('main_service_id') is-invalid @enderror"
                            id="main_service_id" name="main_service_id" required>
                        <option value="">Select Main Service</option>
                        @foreach($mainServices as $mainService)
                            <option value="{{ $mainService->id }}" {{ old('main_service_id') == $mainService->id ? 'selected' : '' }}>
                                {{ $mainService->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('main_service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="sub_service_id" class="form-label">Sub Service</label>
                    <select class="form-select @error('sub_service_id') is-invalid @enderror"
                            id="sub_service_id" name="sub_service_id" disabled>
                        <option value="">Select Sub Service</option>
                    </select>
                    @error('sub_service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="service_package_id" class="form-label">Service Package</label>
                    <select class="form-select @error('service_package_id') is-invalid @enderror"
                            id="service_package_id" name="service_package_id" disabled>
                        <option value="">Select Package</option>
                    </select>
                    @error('service_package_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Package Services Display -->
            <div id="package-services-section" class="mt-3" style="display: none;">
                <h6 class="mb-3"><i class="fas fa-list-check me-2"></i>Package Services</h6>
                <div id="package-services-list" class="service-list"></div>
            </div>

            <!-- Custom Services Selection -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Additional Custom Services</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-custom-services-btn">
                        <i class="fas fa-plus me-1"></i>Add Custom Services
                    </button>
                </div>
                <div id="custom-services-list" class="service-list"></div>
            </div>
        </div>

        <!-- Documents -->
        <div class="dashboard-card mb-4">
            <h5 class="mb-3"><i class="fas fa-file-upload me-2"></i>Required Documents</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="document_mulkiya" class="form-label">
                        Project Mulkiya (Property Title Deed) <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="form-control @error('documents.project_mulkiya') is-invalid @enderror"
                           id="document_mulkiya" name="documents[project_mulkiya]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                    @error('documents.project_mulkiya')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="document_kuroki" class="form-label">
                        Project Kuroki (Project Sketch/Plan) <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="form-control @error('documents.project_kuroki') is-invalid @enderror"
                           id="document_kuroki" name="documents[project_kuroki]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                    @error('documents.project_kuroki')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h6 class="mb-3 mt-3"><i class="fas fa-file me-2"></i>Optional Documents</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="document_location_map" class="form-label">Location Map</label>
                    <input type="file" class="form-control @error('documents.project_location_map') is-invalid @enderror"
                           id="document_location_map" name="documents[project_location_map]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="document_noc" class="form-label">NOC (No Objection Certificate)</label>
                    <input type="file" class="form-control @error('documents.project_noc') is-invalid @enderror"
                           id="document_noc" name="documents[project_noc]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="document_municipality_approval" class="form-label">Municipality Approval</label>
                    <input type="file" class="form-control @error('documents.project_municipality_approval') is-invalid @enderror"
                           id="document_municipality_approval" name="documents[project_municipality_approval]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="document_building_permit" class="form-label">Building Permit</label>
                    <input type="file" class="form-control @error('documents.project_building_permit') is-invalid @enderror"
                           id="document_building_permit" name="documents[project_building_permit]"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Create Project & Generate Contract
            </button>
        </div>
    </form>
</div>

<!-- Custom Services Selection Modal -->
<div class="modal fade" id="customServicesModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Additional Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="all-services-list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-custom-services">
                    <i class="fas fa-check me-2"></i>Add Selected Services
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
    .btn-primary:hover { background-color: var(--hover-color); border-color: var(--hover-color); }
    .form-label { font-weight: 500; color: var(--primary-color); }

    .service-list {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
        background-color: #f8f9fa;
    }

    .service-stage {
        margin-bottom: 20px;
    }

    .service-stage-title {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 2px solid var(--secondary-color);
    }

    .service-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
        margin-bottom: 8px;
        border: 1px solid #e0e0e0;
    }

    .service-item.from-package {
        background-color: #e3f2fd;
        border-color: #90caf9;
    }

    .service-item.custom {
        background-color: #fff3e0;
        border-color: #ffb74d;
    }

    .service-item input[type="checkbox"] {
        margin-right: 10px;
    }

    .service-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: auto;
    }

    .badge-package {
        background-color: #2196f3;
        color: white;
    }

    .badge-custom {
        background-color: #ff9800;
        color: white;
    }

    .service-item .btn-remove {
        margin-left: auto;
        padding: 2px 8px;
        font-size: 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script>
let selectedCustomServices = [];
let packageServices = [];

document.addEventListener('DOMContentLoaded', function() {
    const mainServiceSelect = document.getElementById('main_service_id');
    const subServiceSelect = document.getElementById('sub_service_id');
    const packageSelect = document.getElementById('service_package_id');
    const packageServicesSection = document.getElementById('package-services-section');
    const packageServicesList = document.getElementById('package-services-list');
    const customServicesList = document.getElementById('custom-services-list');
    const addCustomServicesBtn = document.getElementById('add-custom-services-btn');
    const customServicesModal = new bootstrap.Modal(document.getElementById('customServicesModal'));

    // Main Service Change
    mainServiceSelect.addEventListener('change', async function() {
        const mainServiceId = this.value;

        // Reset dependent fields
        subServiceSelect.innerHTML = '<option value="">Select Sub Service</option>';
        packageSelect.innerHTML = '<option value="">Select Package</option>';
        subServiceSelect.disabled = true;
        packageSelect.disabled = true;
        packageServicesSection.style.display = 'none';

        if (!mainServiceId) return;

        try {
            // Load sub-services
            const subServicesResponse = await fetch(`/admin/api/services/sub-services/${mainServiceId}`);
            const subServices = await subServicesResponse.json();

            if (subServices.length > 0) {
                subServices.forEach(subService => {
                    const option = document.createElement('option');
                    option.value = subService.id;
                    option.textContent = subService.name;
                    subServiceSelect.appendChild(option);
                });
                subServiceSelect.disabled = false;
            } else {
                // No sub-services, load packages directly for main service
                loadPackages(mainServiceId, null);
            }
        } catch (error) {
            console.error('Error loading sub-services:', error);
        }
    });

    // Sub Service Change
    subServiceSelect.addEventListener('change', function() {
        const mainServiceId = mainServiceSelect.value;
        const subServiceId = this.value;

        packageSelect.innerHTML = '<option value="">Select Package</option>';
        packageSelect.disabled = true;
        packageServicesSection.style.display = 'none';

        if (subServiceId) {
            loadPackages(mainServiceId, subServiceId);
        }
    });

    // Package Change
    packageSelect.addEventListener('change', async function() {
        const packageId = this.value;

        if (!packageId) {
            packageServicesSection.style.display = 'none';
            packageServices = [];
            return;
        }

        try {
            const response = await fetch(`/admin/api/services/package-services/${packageId}`);
            const services = await response.json();

            packageServices = [];
            packageServicesList.innerHTML = '';

            Object.entries(services).forEach(([stageName, stageServices]) => {
                const stageDiv = document.createElement('div');
                stageDiv.className = 'service-stage';

                const stageTitle = document.createElement('div');
                stageTitle.className = 'service-stage-title';
                stageTitle.textContent = stageName;
                stageDiv.appendChild(stageTitle);

                stageServices.forEach(service => {
                    packageServices.push(service.id);

                    const serviceDiv = document.createElement('div');
                    serviceDiv.className = 'service-item from-package';
                    serviceDiv.innerHTML = `
                        <input type="checkbox" checked disabled class="form-check-input">
                        <span>${service.name}</span>
                        <span class="service-badge badge-package ms-auto">Package</span>
                    `;
                    stageDiv.appendChild(serviceDiv);
                });

                packageServicesList.appendChild(stageDiv);
            });

            packageServicesSection.style.display = 'block';
        } catch (error) {
            console.error('Error loading package services:', error);
        }
    });

    // Add Custom Services Button
    addCustomServicesBtn.addEventListener('click', async function() {
        try {
            const response = await fetch('/admin/api/services/all');
            const allServices = await response.json();

            const allServicesList = document.getElementById('all-services-list');
            allServicesList.innerHTML = '';

            Object.entries(allServices).forEach(([stageName, stageServices]) => {
                const stageDiv = document.createElement('div');
                stageDiv.className = 'service-stage';

                const stageTitle = document.createElement('div');
                stageTitle.className = 'service-stage-title';
                stageTitle.textContent = stageName;
                stageDiv.appendChild(stageTitle);

                stageServices.forEach(service => {
                    // Skip if already in package
                    if (packageServices.includes(service.id)) return;

                    const serviceDiv = document.createElement('div');
                    serviceDiv.className = 'service-item';

                    const isSelected = selectedCustomServices.includes(service.id);
                    serviceDiv.innerHTML = `
                        <input type="checkbox" class="form-check-input custom-service-checkbox"
                               value="${service.id}" ${isSelected ? 'checked' : ''}>
                        <span>${service.name}</span>
                    `;
                    stageDiv.appendChild(serviceDiv);
                });

                allServicesList.appendChild(stageDiv);
            });

            customServicesModal.show();
        } catch (error) {
            console.error('Error loading all services:', error);
        }
    });

    // Confirm Custom Services
    document.getElementById('confirm-custom-services').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.custom-service-checkbox:checked');
        selectedCustomServices = Array.from(checkboxes).map(cb => parseInt(cb.value));

        updateCustomServicesList();
        customServicesModal.hide();
    });

    async function loadPackages(mainServiceId, subServiceId) {
        try {
            const params = new URLSearchParams();
            if (subServiceId) {
                params.append('sub_service_id', subServiceId);
            } else {
                params.append('main_service_id', mainServiceId);
            }

            const response = await fetch(`/admin/api/services/packages?${params}`);
            const packages = await response.json();

            packages.forEach(pkg => {
                const option = document.createElement('option');
                option.value = pkg.id;
                option.textContent = `${pkg.name} - ${pkg.description}`;
                packageSelect.appendChild(option);
            });

            packageSelect.disabled = false;
        } catch (error) {
            console.error('Error loading packages:', error);
        }
    }

    async function updateCustomServicesList() {
        if (selectedCustomServices.length === 0) {
            customServicesList.innerHTML = '<p class="text-muted text-center my-3">No custom services added</p>';
            return;
        }

        try {
            const response = await fetch('/admin/api/services/all');
            const allServices = await response.json();

            customServicesList.innerHTML = '';

            // Flatten services
            const servicesMap = {};
            Object.entries(allServices).forEach(([stageName, stageServices]) => {
                stageServices.forEach(service => {
                    servicesMap[service.id] = { ...service, stage: stageName };
                });
            });

            // Group selected services by stage
            const groupedCustomServices = {};
            selectedCustomServices.forEach(serviceId => {
                const service = servicesMap[serviceId];
                if (service) {
                    if (!groupedCustomServices[service.stage]) {
                        groupedCustomServices[service.stage] = [];
                    }
                    groupedCustomServices[service.stage].push(service);
                }
            });

            // Display grouped services
            Object.entries(groupedCustomServices).forEach(([stageName, services]) => {
                const stageDiv = document.createElement('div');
                stageDiv.className = 'service-stage';

                const stageTitle = document.createElement('div');
                stageTitle.className = 'service-stage-title';
                stageTitle.textContent = stageName;
                stageDiv.appendChild(stageTitle);

                services.forEach(service => {
                    const serviceDiv = document.createElement('div');
                    serviceDiv.className = 'service-item custom';
                    serviceDiv.innerHTML = `
                        <input type="hidden" name="custom_services[]" value="${service.id}">
                        <span>${service.name}</span>
                        <span class="service-badge badge-custom">Custom</span>
                        <button type="button" class="btn btn-sm btn-danger btn-remove"
                                onclick="removeCustomService(${service.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    stageDiv.appendChild(serviceDiv);
                });

                customServicesList.appendChild(stageDiv);
            });
        } catch (error) {
            console.error('Error updating custom services list:', error);
        }
    }

    // Make removeCustomService global
    window.removeCustomService = function(serviceId) {
        selectedCustomServices = selectedCustomServices.filter(id => id !== serviceId);
        updateCustomServicesList();
    };
});
</script>
@endpush
