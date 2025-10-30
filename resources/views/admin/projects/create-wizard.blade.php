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

    <!-- Wizard Progress -->
    <div class="dashboard-card mb-4">
        <div class="wizard-progress">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-icon"><i class="fas fa-info-circle"></i></div>
                <div class="wizard-step-title">Basic Info</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-icon"><i class="fas fa-cogs"></i></div>
                <div class="wizard-step-title">Services</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-icon"><i class="fas fa-calendar"></i></div>
                <div class="wizard-step-title">Details</div>
            </div>
            <div class="wizard-step" data-step="4">
                <div class="wizard-step-icon"><i class="fas fa-file"></i></div>
                <div class="wizard-step-title">Documents</div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" id="projectWizardForm">
        @csrf

        <!-- Step 1: Basic Information -->
        <div class="wizard-content active" data-step="1">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>

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
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
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
            </div>
        </div>

        <!-- Step 2: Services -->
        <div class="wizard-content" data-step="2">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-cogs me-2"></i>Services & Packages</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
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

                    <div class="col-md-6 mb-3">
                        <label for="sub_service_id" class="form-label">Sub Service</label>
                        <select class="form-select @error('sub_service_id') is-invalid @enderror"
                                id="sub_service_id" name="sub_service_id">
                            <option value="">Select Sub Service</option>
                        </select>
                        @error('sub_service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="service_package_id" class="form-label">Service Package (Optional)</label>
                    <select class="form-select @error('service_package_id') is-invalid @enderror"
                            id="service_package_id" name="service_package_id">
                        <option value="">No Package - Select Services Manually</option>
                    </select>
                    @error('service_package_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Choose a package for pre-configured services, or select services individually below.</small>
                </div>

                <!-- Package Services Preview (shown when package selected) -->
                <div id="packageServicesPreview" class="mb-4" style="display: none;">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-box-open me-2"></i>Package Services</h6>
                        <div id="packageServicesList"></div>
                        @if(config('project.creation.editable_package_services', true))
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle"></i> You can remove services from the package by unchecking them below.
                        </small>
                        @endif
                    </div>
                </div>

                <!-- Service Selection Tabs -->
                <div class="service-selection-tabs">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="section-tab" data-bs-toggle="tab" data-bs-target="#section-selection" type="button" role="tab">
                                <i class="fas fa-layer-group me-1"></i> Select by Section
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual-selection" type="button" role="tab">
                                <i class="fas fa-list me-1"></i> Select Individual Services
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Section Selection -->
                        <div class="tab-pane fade show active" id="section-selection" role="tabpanel">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle"></i> Select entire sections/stages at once for faster setup.
                            </p>
                            <div id="sectionSelectionContainer">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="ms-2">Loading service stages...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Service Selection -->
                        <div class="tab-pane fade" id="individual-selection" role="tabpanel">
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle"></i> Select specific services individually.
                            </p>
                            <div id="individualServicesContainer">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="ms-2">Loading services...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Project Details -->
        <div class="wizard-content" data-step="3">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-calendar me-2"></i>Project Details</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="budget" class="form-label">Budget (OMR)</label>
                        <input type="number" class="form-control @error('budget') is-invalid @enderror"
                               id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0">
                        @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                               id="location" name="location" value="{{ old('location') }}">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date" value="{{ old('start_date') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date" value="{{ old('end_date') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
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

                @if(!config('project.creation.auto_generate_contract', false))
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="generate_contract" name="generate_contract" value="1"
                               {{ old('generate_contract', config('project.creation.generate_contract_by_default', true)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="generate_contract">
                            Automatically generate contract for this project
                        </label>
                        <small class="d-block text-muted mt-1">
                            <i class="fas fa-info-circle"></i> If checked, a draft contract will be created automatically with all selected services.
                        </small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Step 4: Documents -->
        <div class="wizard-content" data-step="4">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-file me-2"></i>Project Documents</h5>

                <p class="text-muted mb-4">
                    <i class="fas fa-info-circle"></i> Upload project documents. You can also add documents later from the project page.
                </p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="project_mulkiya" class="form-label">Project Mulkiya (Title Deed)</label>
                        <input type="file" class="form-control @error('documents.project_mulkiya') is-invalid @enderror"
                               id="project_mulkiya" name="documents[project_mulkiya]" accept=".pdf,.jpg,.jpeg,.png">
                        @error('documents.project_mulkiya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="project_kuroki" class="form-label">Project Kuroki (Sketch/Plan)</label>
                        <input type="file" class="form-control @error('documents.project_kuroki') is-invalid @enderror"
                               id="project_kuroki" name="documents[project_kuroki]" accept=".pdf,.jpg,.jpeg,.png">
                        @error('documents.project_kuroki')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="location_map" class="form-label">Location Map</label>
                        <input type="file" class="form-control @error('documents.location_map') is-invalid @enderror"
                               id="location_map" name="documents[location_map]" accept=".pdf,.jpg,.jpeg,.png">
                        @error('documents.location_map')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="noc" class="form-label">NOC (No Objection Certificate)</label>
                        <input type="file" class="form-control @error('documents.noc') is-invalid @enderror"
                               id="noc" name="documents[noc]" accept=".pdf,.jpg,.jpeg,.png">
                        @error('documents.noc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip:</strong> All documents are optional. You can skip this step and add documents later from the project page.
                </div>
            </div>
        </div>

        <!-- Wizard Navigation -->
        <div class="dashboard-card">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                    <i class="fas fa-arrow-left me-1"></i> Previous
                </button>
                <div class="flex-fill"></div>
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                    Next <i class="fas fa-arrow-right ms-1"></i>
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                    <i class="fas fa-check me-1"></i> Create Project
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.wizard-progress {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 20px 0;
}

.wizard-progress::before {
    content: '';
    position: absolute;
    top: 50px;
    left: 12.5%;
    right: 12.5%;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}

.wizard-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.wizard-step-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 24px;
    color: #6c757d;
    transition: all 0.3s;
}

.wizard-step-title {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.wizard-step.active .wizard-step-icon {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.wizard-step.active .wizard-step-title {
    color: var(--primary-color);
    font-weight: 600;
}

.wizard-step.completed .wizard-step-icon {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.wizard-step.completed .wizard-step-title {
    color: #28a745;
}

.wizard-content {
    display: none;
}

.wizard-content.active {
    display: block;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.service-section {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.2s;
}

.service-section:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.service-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 10px;
}

.service-section-header h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.service-section-badge {
    background: var(--primary-color);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.service-list {
    padding-left: 30px;
    display: none;
}

.service-list.show {
    display: block;
}

.service-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.service-item:last-child {
    border-bottom: none;
}

.select-all-section {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.select-all-section:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.service-count {
    font-size: 12px;
    color: #6c757d;
    margin-left: 5px;
}
</style>

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.wizard-step').forEach(stepEl => {
        stepEl.classList.remove('active');
    });

    // Show current step
    document.querySelector(`.wizard-content[data-step="${step}"]`).classList.add('active');
    document.querySelector(`.wizard-step[data-step="${step}"]`).classList.add('active');

    // Mark previous steps as completed
    for (let i = 1; i < step; i++) {
        document.querySelector(`.wizard-step[data-step="${i}"]`).classList.add('completed');
    }

    // Update buttons
    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
}

function changeStep(direction) {
    const newStep = currentStep + direction;
    if (newStep >= 1 && newStep <= totalSteps) {
        // Validate current step before moving forward
        if (direction > 0 && !validateStep(currentStep)) {
            return;
        }
        currentStep = newStep;
        showStep(currentStep);
    }
}

function validateStep(step) {
    const stepElement = document.querySelector(`.wizard-content[data-step="${step}"]`);
    const requiredInputs = stepElement.querySelectorAll('[required]');

    for (let input of requiredInputs) {
        if (!input.value || (input.type === 'select-one' && input.value === '')) {
            input.classList.add('is-invalid');
            input.focus();
            return false;
        } else {
            input.classList.remove('is-invalid');
        }
    }
    return true;
}

// Service loading functions
let allServices = {};
let allStages = {};
let selectedServices = new Set();
let selectedSections = new Set();

document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
    loadServiceStages();
    loadAllServices();

    // Main service change handler
    document.getElementById('main_service_id').addEventListener('change', function() {
        loadSubServices(this.value);
        loadPackages(this.value, null);
    });

    // Sub service change handler
    document.getElementById('sub_service_id').addEventListener('change', function() {
        const mainServiceId = document.getElementById('main_service_id').value;
        loadPackages(mainServiceId, this.value);
    });

    // Package change handler
    document.getElementById('service_package_id').addEventListener('change', function() {
        if (this.value) {
            loadPackageServices(this.value);
        } else {
            document.getElementById('packageServicesPreview').style.display = 'none';
        }
    });
});

function loadServiceStages() {
    fetch('{{ route("admin.api.services.stages") }}')
        .then(response => response.json())
        .then(stages => {
            allStages = stages;
            renderServiceStages(stages);
        })
        .catch(error => {
            console.error('Error loading service stages:', error);
            document.getElementById('sectionSelectionContainer').innerHTML =
                '<div class="alert alert-danger">Failed to load service stages</div>';
        });
}

function renderServiceStages(stages) {
    const container = document.getElementById('sectionSelectionContainer');
    if (stages.length === 0) {
        container.innerHTML = '<p class="text-muted">No service stages available</p>';
        return;
    }

    let html = '';
    stages.forEach(stage => {
        html += `
            <div class="service-section">
                <div class="service-section-header" onclick="toggleSection('${stage.id}')">
                    <div>
                        <h6>${stage.name}</h6>
                        <small class="text-muted">${stage.description || ''}</small>
                        <span class="service-count">(${stage.service_count} services)</span>
                    </div>
                    <button type="button" class="select-all-section" onclick="event.stopPropagation(); selectAllInSection('${stage.id}')">
                        Select All
                    </button>
                </div>
                <div class="service-list" id="section-${stage.id}">
                    ${stage.services.map(service => `
                        <div class="service-item">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input section-service-check"
                                       data-section="${stage.id}" value="${service.id}"
                                       name="custom_services[]" id="service-${service.id}">
                                <label class="form-check-label" for="service-${service.id}">
                                    ${service.name}
                                </label>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function toggleSection(sectionId) {
    const sectionList = document.getElementById(`section-${sectionId}`);
    sectionList.classList.toggle('show');
}

function selectAllInSection(sectionId) {
    const checkboxes = document.querySelectorAll(`[data-section="${sectionId}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });

    // Add section to selected sections for backend processing
    const sectionInput = document.createElement('input');
    sectionInput.type = 'hidden';
    sectionInput.name = 'selected_sections[]';
    sectionInput.value = sectionId;
    sectionInput.className = 'section-selection-input';

    // Remove old inputs for this section
    document.querySelectorAll(`.section-selection-input[value="${sectionId}"]`).forEach(el => el.remove());

    if (!allChecked) {
        document.getElementById('projectWizardForm').appendChild(sectionInput);
    }
}

function loadAllServices() {
    fetch('{{ route("admin.api.services.all") }}')
        .then(response => response.json())
        .then(services => {
            allServices = services;
            renderIndividualServices(services);
        })
        .catch(error => {
            console.error('Error loading services:', error);
            document.getElementById('individualServicesContainer').innerHTML =
                '<div class="alert alert-danger">Failed to load services</div>';
        });
}

function renderIndividualServices(services) {
    const container = document.getElementById('individualServicesContainer');

    let html = '';
    Object.keys(services).forEach(stageName => {
        html += `
            <div class="service-section mb-3">
                <h6 class="mb-2">${stageName}</h6>
                ${services[stageName].map(service => `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" value="${service.id}"
                               name="custom_services[]" id="individual-service-${service.id}">
                        <label class="form-check-label" for="individual-service-${service.id}">
                            ${service.name}
                        </label>
                    </div>
                `).join('')}
            </div>
        `;
    });

    container.innerHTML = html || '<p class="text-muted">No services available</p>';
}

function loadSubServices(mainServiceId) {
    if (!mainServiceId) {
        document.getElementById('sub_service_id').innerHTML = '<option value="">Select Sub Service</option>';
        return;
    }

    fetch(`/admin/api/services/sub-services/${mainServiceId}`)
        .then(response => response.json())
        .then(subServices => {
            let options = '<option value="">Select Sub Service</option>';
            subServices.forEach(sub => {
                options += `<option value="${sub.id}">${sub.name}</option>`;
            });
            document.getElementById('sub_service_id').innerHTML = options;
        });
}

function loadPackages(mainServiceId, subServiceId) {
    if (!mainServiceId) return;

    let url = '{{ route("admin.api.services.packages") }}?main_service_id=' + mainServiceId;
    if (subServiceId) {
        url += '&sub_service_id=' + subServiceId;
    }

    fetch(url)
        .then(response => response.json())
        .then(packages => {
            let options = '<option value="">No Package - Select Services Manually</option>';
            packages.forEach(pkg => {
                options += `<option value="${pkg.id}">${pkg.name}</option>`;
            });
            document.getElementById('service_package_id').innerHTML = options;
        });
}

function loadPackageServices(packageId) {
    fetch(`/admin/api/services/package-services/${packageId}`)
        .then(response => response.json())
        .then(services => {
            displayPackageServices(services);
        });
}

function displayPackageServices(services) {
    const previewDiv = document.getElementById('packageServicesPreview');
    const listDiv = document.getElementById('packageServicesList');

    let html = '';
    Object.keys(services).forEach(stageName => {
        html += `<div class="mb-2"><strong>${stageName}:</strong> `;
        html += services[stageName].map(s => s.name).join(', ');
        html += '</div>';
    });

    listDiv.innerHTML = html;
    previewDiv.style.display = 'block';
}
</script>
@endpush
@endsection
