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

    <!-- Wizard Progress - Simplified to 3 Steps -->
    <div class="dashboard-card mb-4">
        <div class="wizard-progress">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-icon"><i class="fas fa-info-circle"></i></div>
                <div class="wizard-step-title">Project Details</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-icon"><i class="fas fa-cogs"></i></div>
                <div class="wizard-step-title">Services</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-icon"><i class="fas fa-check-circle"></i></div>
                <div class="wizard-step-title">Review & Create</div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" id="projectWizardForm">
        @csrf

        <!-- Step 1: Project Details (Merged Basic Info + Details) -->
        <div class="wizard-content active" data-step="1">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Project Details</h5>

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

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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
                </div>

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
                               id="location" name="location" value="{{ old('location') }}" placeholder="e.g., Muscat, Oman">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3" placeholder="Brief project description...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Services (Simplified - Single selection method) -->
        <div class="wizard-content" data-step="2">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-cogs me-2"></i>Select Services</h5>

                <div class="row mb-4">
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
                                id="sub_service_id" name="sub_service_id">
                            <option value="">Select Sub Service</option>
                        </select>
                        @error('sub_service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="service_package_id" class="form-label">Service Package</label>
                        <select class="form-select @error('service_package_id') is-invalid @enderror"
                                id="service_package_id" name="service_package_id">
                            <option value="">Select a Package (Optional)</option>
                        </select>
                        @error('service_package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Package Services Preview -->
                <div id="packageServicesPreview" class="mb-4" style="display: none;">
                    <div class="alert alert-success">
                        <h6 class="mb-2"><i class="fas fa-box-open me-2"></i>Package Includes:</h6>
                        <div id="packageServicesList"></div>
                    </div>
                </div>

                <!-- Service Selection by Stage -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Or Select Individual Services:</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="expandAllSections()">
                                <i class="fas fa-expand-alt me-1"></i> Expand All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="collapseAllSections()">
                                <i class="fas fa-compress-alt me-1"></i> Collapse All
                            </button>
                        </div>
                    </div>

                    <div id="serviceStagesContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span class="ms-2">Loading service stages...</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tip:</strong> Select a package for quick setup, or pick individual services for full control.
                    Selected services will be shown in the review step.
                </div>
            </div>
        </div>

        <!-- Step 3: Review & Create -->
        <div class="wizard-content" data-step="3">
            <div class="dashboard-card">
                <h5 class="mb-4"><i class="fas fa-check-circle me-2"></i>Review & Create</h5>

                <p class="text-muted mb-4">Review your project details before creating. You can go back to make changes.</p>

                <!-- Project Summary -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Project Information</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Name:</td>
                                    <td id="review-name" class="fw-semibold">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Number:</td>
                                    <td id="review-number">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Client:</td>
                                    <td id="review-client">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Project Manager:</td>
                                    <td id="review-pm">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td id="review-status">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-muted mb-3"><i class="fas fa-calendar me-2"></i>Schedule & Budget</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Start Date:</td>
                                    <td id="review-start">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">End Date:</td>
                                    <td id="review-end">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Budget:</td>
                                    <td id="review-budget">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Location:</td>
                                    <td id="review-location">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Services Summary -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-muted mb-3"><i class="fas fa-cogs me-2"></i>Selected Services</h6>
                    <div id="review-services">
                        <span class="text-muted">No services selected</span>
                    </div>
                </div>

                <!-- Options -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-muted mb-3"><i class="fas fa-sliders-h me-2"></i>Creation Options</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="auto_generate_tasks" name="auto_generate_tasks" value="1" checked>
                                <label class="form-check-label" for="auto_generate_tasks">
                                    Generate tasks from templates
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">Tasks will be created based on selected services</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="auto_assign_tasks" name="auto_assign_tasks" value="1" checked>
                                <label class="form-check-label" for="auto_assign_tasks">
                                    Auto-assign tasks to team
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">Based on skills, availability, and workload</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="generate_contract" name="generate_contract" value="1"
                                       {{ old('generate_contract', config('project.creation.generate_contract_by_default', true)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="generate_contract">
                                    Generate draft contract
                                </label>
                            </div>
                            <small class="text-muted d-block">A draft contract will be created automatically</small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="generate_milestones" name="generate_milestones" value="1" checked>
                                <label class="form-check-label" for="generate_milestones">
                                    Generate milestones
                                </label>
                            </div>
                            <small class="text-muted d-block">Default milestones based on project timeline</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ready to create!</strong> You can add documents and make further adjustments after the project is created.
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
                    <i class="fas fa-plus me-1"></i> Create Project
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
    left: 16%;
    right: 16%;
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

.service-stage-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 12px;
    overflow: hidden;
}

.service-stage-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    background: #f8f9fa;
    cursor: pointer;
    transition: background 0.2s;
}

.service-stage-header:hover {
    background: #e9ecef;
}

.service-stage-header h6 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
}

.service-stage-body {
    padding: 15px;
    display: none;
    border-top: 1px solid #e0e0e0;
}

.service-stage-body.show {
    display: block;
}

.service-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.service-item:last-child {
    border-bottom: none;
}

.service-count-badge {
    background: var(--primary-color);
    color: white;
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
}

.selected-services-badge {
    background: #28a745;
    color: white;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 8px;
}
</style>

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 3;

function showStep(step) {
    document.querySelectorAll('.wizard-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.wizard-step').forEach(stepEl => {
        stepEl.classList.remove('active');
    });

    document.querySelector(`.wizard-content[data-step="${step}"]`).classList.add('active');
    document.querySelector(`.wizard-step[data-step="${step}"]`).classList.add('active');

    for (let i = 1; i < step; i++) {
        document.querySelector(`.wizard-step[data-step="${i}"]`).classList.add('completed');
    }

    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';

    if (step === 3) {
        updateReviewSummary();
    }
}

function changeStep(direction) {
    const newStep = currentStep + direction;
    if (newStep >= 1 && newStep <= totalSteps) {
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

function updateReviewSummary() {
    // Project info
    document.getElementById('review-name').textContent = document.getElementById('name').value || '-';
    document.getElementById('review-number').textContent = document.getElementById('project_number').value || '-';

    const clientSelect = document.getElementById('client_id');
    document.getElementById('review-client').textContent = clientSelect.selectedIndex > 0
        ? clientSelect.options[clientSelect.selectedIndex].text : '-';

    const pmSelect = document.getElementById('project_manager_id');
    document.getElementById('review-pm').textContent = pmSelect.selectedIndex > 0
        ? pmSelect.options[pmSelect.selectedIndex].text : 'Not assigned';

    const statusSelect = document.getElementById('status');
    document.getElementById('review-status').textContent = statusSelect.selectedIndex >= 0
        ? statusSelect.options[statusSelect.selectedIndex].text : '-';

    // Schedule & Budget
    document.getElementById('review-start').textContent = document.getElementById('start_date').value || 'Not set';
    document.getElementById('review-end').textContent = document.getElementById('end_date').value || 'Not set';

    const budget = document.getElementById('budget').value;
    document.getElementById('review-budget').textContent = budget ? `OMR ${parseFloat(budget).toLocaleString()}` : 'Not set';
    document.getElementById('review-location').textContent = document.getElementById('location').value || 'Not set';

    // Services
    updateServicesReview();
}

function updateServicesReview() {
    const container = document.getElementById('review-services');
    const selectedServices = [];

    // Package
    const packageSelect = document.getElementById('service_package_id');
    if (packageSelect.value) {
        selectedServices.push({
            type: 'package',
            name: packageSelect.options[packageSelect.selectedIndex].text
        });
    }

    // Main service
    const mainServiceSelect = document.getElementById('main_service_id');
    if (mainServiceSelect.value) {
        selectedServices.push({
            type: 'main',
            name: mainServiceSelect.options[mainServiceSelect.selectedIndex].text
        });
    }

    // Sub service
    const subServiceSelect = document.getElementById('sub_service_id');
    if (subServiceSelect.value) {
        selectedServices.push({
            type: 'sub',
            name: subServiceSelect.options[subServiceSelect.selectedIndex].text
        });
    }

    // Individual services
    document.querySelectorAll('.service-check:checked').forEach(cb => {
        selectedServices.push({
            type: 'service',
            name: cb.dataset.serviceName
        });
    });

    if (selectedServices.length === 0) {
        container.innerHTML = '<span class="text-muted">No services selected</span>';
        return;
    }

    let html = '<div class="d-flex flex-wrap gap-2">';
    selectedServices.forEach(s => {
        const badgeClass = s.type === 'package' ? 'bg-primary' :
                          s.type === 'main' ? 'bg-success' :
                          s.type === 'sub' ? 'bg-info' : 'bg-secondary';
        html += `<span class="badge ${badgeClass}">${s.name}</span>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

// Service loading
document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
    loadServiceStages();

    document.getElementById('main_service_id').addEventListener('change', function() {
        loadSubServices(this.value);
        loadPackages(this.value, null);
    });

    document.getElementById('sub_service_id').addEventListener('change', function() {
        const mainServiceId = document.getElementById('main_service_id').value;
        loadPackages(mainServiceId, this.value);
    });

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
            renderServiceStages(stages);
        })
        .catch(error => {
            console.error('Error loading service stages:', error);
            document.getElementById('serviceStagesContainer').innerHTML =
                '<div class="alert alert-danger">Failed to load service stages</div>';
        });
}

function renderServiceStages(stages) {
    const container = document.getElementById('serviceStagesContainer');
    if (stages.length === 0) {
        container.innerHTML = '<p class="text-muted">No service stages available</p>';
        return;
    }

    let html = '';
    stages.forEach(stage => {
        html += `
            <div class="service-stage-card">
                <div class="service-stage-header" onclick="toggleStage('${stage.id}')">
                    <div>
                        <h6>${stage.name}</h6>
                        <small class="text-muted">${stage.description || ''}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="service-count-badge">${stage.service_count} services</span>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="event.stopPropagation(); selectAllInStage('${stage.id}')">
                            Select All
                        </button>
                        <i class="fas fa-chevron-down" id="chevron-${stage.id}"></i>
                    </div>
                </div>
                <div class="service-stage-body" id="stage-body-${stage.id}">
                    ${stage.services.map(service => `
                        <div class="service-item">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input service-check"
                                       data-stage="${stage.id}" data-service-name="${service.name}"
                                       value="${service.id}" name="custom_services[]"
                                       id="service-${service.id}">
                                <label class="form-check-label" for="service-${service.id}">
                                    ${service.name}
                                    ${service.estimated_hours ? `<small class="text-muted">(~${service.estimated_hours}h)</small>` : ''}
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

function toggleStage(stageId) {
    const body = document.getElementById(`stage-body-${stageId}`);
    const chevron = document.getElementById(`chevron-${stageId}`);

    body.classList.toggle('show');
    chevron.classList.toggle('fa-chevron-down');
    chevron.classList.toggle('fa-chevron-up');
}

function expandAllSections() {
    document.querySelectorAll('.service-stage-body').forEach(body => body.classList.add('show'));
    document.querySelectorAll('[id^="chevron-"]').forEach(chevron => {
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
    });
}

function collapseAllSections() {
    document.querySelectorAll('.service-stage-body').forEach(body => body.classList.remove('show'));
    document.querySelectorAll('[id^="chevron-"]').forEach(chevron => {
        chevron.classList.add('fa-chevron-down');
        chevron.classList.remove('fa-chevron-up');
    });
}

function selectAllInStage(stageId) {
    const checkboxes = document.querySelectorAll(`[data-stage="${stageId}"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
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
            let options = '<option value="">Select a Package (Optional)</option>';
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

    let html = '<div class="d-flex flex-wrap gap-2">';
    Object.keys(services).forEach(stageName => {
        services[stageName].forEach(service => {
            html += `<span class="badge bg-light text-dark border">${service.name}</span>`;
        });
    });
    html += '</div>';

    listDiv.innerHTML = html;
    previewDiv.style.display = 'block';
}
</script>
@endpush
@endsection
