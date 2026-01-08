@extends('layouts.admin')

@section('title', 'Edit Integration')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Edit Integration</h1>
        <p class="text-muted mb-0">Update integration settings for {{ $integration->name }}</p>
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .integration-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .integration-badge.whatsapp { background: #dcf8c6; color: #25d366; }
    .integration-badge.email { background: #e3f2fd; color: #1976d2; }
    .integration-badge.sms { background: #fff3e0; color: #ff9800; }
    .integration-badge.api { background: #f3e5f5; color: #9c27b0; }

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

    .type-display {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .type-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .type-icon.whatsapp { background: #dcf8c6; color: #25d366; }
    .type-icon.email { background: #e3f2fd; color: #1976d2; }
    .type-icon.sms { background: #fff3e0; color: #ff9800; }
    .type-icon.api { background: #f3e5f5; color: #9c27b0; }

    .type-info h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .type-info span {
        font-size: 13px;
        color: #64748b;
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
        justify-content: space-between;
        align-items: center;
        border-radius: 0 0 12px 12px;
    }

    .btn-delete {
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: #ffebee;
        border: none;
        color: #c62828;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete:hover {
        background: #ffcdd2;
    }

    .form-actions {
        display: flex;
        gap: 12px;
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
</style>

<div class="form-card">
    <form action="{{ route('admin.integrations.update', $integration) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-header">
            <h4><i class="fas fa-edit me-2"></i> Integration Details</h4>
            <span class="integration-badge {{ $integration->type }}">{{ ucfirst($integration->type) }}</span>
        </div>

        <div class="form-body">
            <div class="form-group">
                <label class="form-label">
                    Integration Name <span class="required">*</span>
                </label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $integration->name) }}" placeholder="e.g., Company WhatsApp" required>
                @error('name')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Integration Type</label>
                <div class="type-display">
                    <div class="type-icon {{ $integration->type }}">
                        @switch($integration->type)
                            @case('whatsapp')
                                <i class="fab fa-whatsapp"></i>
                                @break
                            @case('email')
                                <i class="fas fa-envelope"></i>
                                @break
                            @case('sms')
                                <i class="fas fa-sms"></i>
                                @break
                            @case('api')
                                <i class="fas fa-code"></i>
                                @break
                        @endswitch
                    </div>
                    <div class="type-info">
                        <h5>{{ ucfirst($integration->type) }} Integration</h5>
                        <span>Type cannot be changed after creation</span>
                    </div>
                </div>
                <input type="hidden" name="type" value="{{ $integration->type }}">
            </div>

            <div class="form-group">
                <label class="form-label">Provider</label>
                <input type="text" name="provider" class="form-control @error('provider') is-invalid @enderror"
                       value="{{ old('provider', $integration->provider) }}" placeholder="e.g., Twilio, SendGrid, etc.">
                <div class="form-hint">The service provider for this integration</div>
                @error('provider')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            @php
                $config = is_string($integration->config) ? json_decode($integration->config, true) : ($integration->config ?? []);
            @endphp

            <div class="config-section">
                <h5><i class="fas fa-cog"></i> Configuration</h5>

                @switch($integration->type)
                    @case('whatsapp')
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="text" name="config[api_key]" class="form-control"
                                   value="{{ $config['api_key'] ?? '' }}"
                                   placeholder="Your WhatsApp Business API key">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number ID</label>
                            <input type="text" name="config[phone_number_id]" class="form-control"
                                   value="{{ $config['phone_number_id'] ?? '' }}"
                                   placeholder="WhatsApp phone number ID">
                        </div>
                        @break

                    @case('email')
                        <div class="form-group">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="config[smtp_host]" class="form-control"
                                   value="{{ $config['smtp_host'] ?? '' }}"
                                   placeholder="e.g., smtp.gmail.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP Port</label>
                            <input type="text" name="config[smtp_port]" class="form-control"
                                   value="{{ $config['smtp_port'] ?? '' }}"
                                   placeholder="e.g., 587">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="config[smtp_username]" class="form-control"
                                   value="{{ $config['smtp_username'] ?? '' }}"
                                   placeholder="SMTP username">
                        </div>
                        @break

                    @case('sms')
                        <div class="form-group">
                            <label class="form-label">Account SID</label>
                            <input type="text" name="config[account_sid]" class="form-control"
                                   value="{{ $config['account_sid'] ?? '' }}"
                                   placeholder="Twilio Account SID">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Auth Token</label>
                            <input type="text" name="config[auth_token]" class="form-control"
                                   value="{{ $config['auth_token'] ?? '' }}"
                                   placeholder="Twilio Auth Token">
                        </div>
                        <div class="form-group">
                            <label class="form-label">From Number</label>
                            <input type="text" name="config[from_number]" class="form-control"
                                   value="{{ $config['from_number'] ?? '' }}"
                                   placeholder="+1234567890">
                        </div>
                        @break

                    @case('api')
                        <div class="form-group">
                            <label class="form-label">Base URL</label>
                            <input type="text" name="config[base_url]" class="form-control"
                                   value="{{ $config['base_url'] ?? '' }}"
                                   placeholder="https://api.example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">API Key</label>
                            <input type="text" name="config[api_key]" class="form-control"
                                   value="{{ $config['api_key'] ?? '' }}"
                                   placeholder="Your API key">
                        </div>
                        <div class="form-group">
                            <label class="form-label">API Secret</label>
                            <input type="text" name="config[api_secret]" class="form-control"
                                   value="{{ $config['api_secret'] ?? '' }}"
                                   placeholder="Your API secret">
                        </div>
                        @break
                @endswitch
            </div>

            <div class="form-group" style="margin-top: 24px;">
                <label class="form-label">Status</label>
                <div class="toggle-wrapper">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $integration->is_active) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Integration is active and ready to use</span>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <form action="{{ route('admin.integrations.destroy', $integration) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this integration? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>

            <div class="form-actions">
                <a href="{{ route('admin.integrations.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-check me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
