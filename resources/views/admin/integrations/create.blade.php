@extends('layouts.admin')

@section('title', 'Add Integration')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Add Integration</h1>
        <p class="text-muted mb-0">Configure a new third-party service integration</p>
    </div>
    <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Integrations
    </a>
</div>
@endsection

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        max-width: 700px;
    }

    .form-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .form-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .form-label .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color, #2f0e13);
        box-shadow: 0 0 0 3px rgba(47, 14, 19, 0.1);
    }

    .form-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .type-selector {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .type-option {
        position: relative;
    }

    .type-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .type-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .type-option label:hover {
        border-color: var(--primary-color, #2f0e13);
    }

    .type-option input:checked + label {
        border-color: var(--primary-color, #2f0e13);
        background: rgba(47, 14, 19, 0.05);
    }

    .type-option .type-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 10px;
    }

    .type-option .type-icon.whatsapp { background: #dcf8c6; color: #25d366; }
    .type-option .type-icon.email { background: #e3f2fd; color: #1976d2; }
    .type-option .type-icon.sms { background: #fff3e0; color: #ff9800; }
    .type-option .type-icon.api { background: #f3e5f5; color: #9c27b0; }

    .type-option .type-name {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }

    .config-section {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px;
        margin-top: 24px;
    }

    .config-section h5 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .config-section h5 i {
        color: var(--primary-color, #2f0e13);
    }

    .config-fields {
        display: none;
    }

    .config-fields.active {
        display: block;
    }

    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #e2e8f0;
        border-radius: 26px;
        transition: 0.3s;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: #4caf50;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    .toggle-label {
        font-size: 14px;
        color: #1e293b;
    }

    .form-footer {
        padding: 20px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        border-radius: 0 0 12px 12px;
    }

    .btn-cancel {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .btn-save {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: var(--primary-color, #2f0e13);
        border: none;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save:hover {
        background: #1a0508;
    }

    @media (max-width: 640px) {
        .type-selector {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="form-card">
    <form action="{{ route('admin.integrations.store') }}" method="POST">
        @csrf

        <div class="form-header">
            <h4><i class="fas fa-plug me-2"></i> Integration Details</h4>
        </div>

        <div class="form-body">
            <div class="form-group">
                <label class="form-label">
                    Integration Name <span class="required">*</span>
                </label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g., Company WhatsApp" required>
                @error('name')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Integration Type <span class="required">*</span>
                </label>
                <div class="type-selector">
                    <div class="type-option">
                        <input type="radio" name="type" id="type-whatsapp" value="whatsapp"
                               {{ old('type') == 'whatsapp' ? 'checked' : '' }}>
                        <label for="type-whatsapp">
                            <div class="type-icon whatsapp"><i class="fab fa-whatsapp"></i></div>
                            <span class="type-name">WhatsApp</span>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-email" value="email"
                               {{ old('type', 'email') == 'email' ? 'checked' : '' }}>
                        <label for="type-email">
                            <div class="type-icon email"><i class="fas fa-envelope"></i></div>
                            <span class="type-name">Email</span>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-sms" value="sms"
                               {{ old('type') == 'sms' ? 'checked' : '' }}>
                        <label for="type-sms">
                            <div class="type-icon sms"><i class="fas fa-sms"></i></div>
                            <span class="type-name">SMS</span>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-api" value="api"
                               {{ old('type') == 'api' ? 'checked' : '' }}>
                        <label for="type-api">
                            <div class="type-icon api"><i class="fas fa-code"></i></div>
                            <span class="type-name">API</span>
                        </label>
                    </div>
                </div>
                @error('type')
                    <div class="text-danger mt-2" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Provider</label>
                <input type="text" name="provider" class="form-control @error('provider') is-invalid @enderror"
                       value="{{ old('provider') }}" placeholder="e.g., Twilio, SendGrid, etc.">
                <div class="form-hint">The service provider for this integration</div>
                @error('provider')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="config-section">
                <h5><i class="fas fa-cog"></i> Configuration</h5>

                <!-- WhatsApp Config -->
                <div class="config-fields" id="config-whatsapp">
                    <div class="form-group">
                        <label class="form-label">API Key</label>
                        <input type="text" name="config[api_key]" class="form-control"
                               placeholder="Your WhatsApp Business API key">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number ID</label>
                        <input type="text" name="config[phone_number_id]" class="form-control"
                               placeholder="WhatsApp phone number ID">
                    </div>
                </div>

                <!-- Email Config -->
                <div class="config-fields active" id="config-email">
                    <div class="form-group">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="config[smtp_host]" class="form-control"
                               placeholder="e.g., smtp.gmail.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Port</label>
                        <input type="text" name="config[smtp_port]" class="form-control"
                               placeholder="e.g., 587">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="config[smtp_username]" class="form-control"
                               placeholder="SMTP username">
                    </div>
                </div>

                <!-- SMS Config -->
                <div class="config-fields" id="config-sms">
                    <div class="form-group">
                        <label class="form-label">Account SID</label>
                        <input type="text" name="config[account_sid]" class="form-control"
                               placeholder="Twilio Account SID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Auth Token</label>
                        <input type="text" name="config[auth_token]" class="form-control"
                               placeholder="Twilio Auth Token">
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Number</label>
                        <input type="text" name="config[from_number]" class="form-control"
                               placeholder="+1234567890">
                    </div>
                </div>

                <!-- API Config -->
                <div class="config-fields" id="config-api">
                    <div class="form-group">
                        <label class="form-label">Base URL</label>
                        <input type="text" name="config[base_url]" class="form-control"
                               placeholder="https://api.example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Key</label>
                        <input type="text" name="config[api_key]" class="form-control"
                               placeholder="Your API key">
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Secret</label>
                        <input type="text" name="config[api_secret]" class="form-control"
                               placeholder="Your API secret">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label">Status</label>
                <div class="toggle-wrapper">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Integration is active and ready to use</span>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.integrations.index') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">
                <i class="fas fa-check me-1"></i> Create Integration
            </button>
        </div>
    </form>
</div>

<script>
    // Show/hide config fields based on selected type
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.config-fields').forEach(f => f.classList.remove('active'));
            document.getElementById('config-' + this.value).classList.add('active');
        });
    });

    // Initialize on page load
    const checkedType = document.querySelector('input[name="type"]:checked');
    if (checkedType) {
        document.getElementById('config-' + checkedType.value).classList.add('active');
    }
</script>
@endsection
