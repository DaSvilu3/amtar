@extends('layouts.admin')

@section('title', 'Integrations')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Integrations</h1>
        <p class="text-muted mb-0">Email and WhatsApp notification settings</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .integration-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        margin-bottom: 20px;
    }

    .integration-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .integration-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
    }

    .integration-info h4 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .integration-info p {
        font-size: 13px;
        color: #64748b;
        margin: 4px 0 0 0;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: auto;
    }

    .status-badge.enabled {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-badge.disabled {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-badge.not-configured {
        background: #fef3c7;
        color: #d97706;
    }

    .config-section {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px;
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

    .config-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .config-item:last-child {
        border-bottom: none;
    }

    .config-label {
        font-size: 13px;
        color: #64748b;
    }

    .config-value {
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
        font-family: monospace;
    }

    .config-value.masked {
        color: #94a3b8;
    }

    .btn-test {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        background: white;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 16px;
    }

    .btn-test:hover {
        background: #f1f5f9;
    }

    .btn-test:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .test-result {
        margin-top: 12px;
        padding: 12px;
        border-radius: 8px;
        font-size: 13px;
        display: none;
    }

    .test-result.success {
        background: #dcfce7;
        color: #16a34a;
    }

    .test-result.error {
        background: #fee2e2;
        color: #dc2626;
    }

    .env-hint {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        font-size: 13px;
        color: #92400e;
    }

    .env-hint code {
        background: #fef3c7;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
    }
</style>

<div class="row">
    @foreach($integrations as $integration)
    <div class="col-md-6">
        <div class="integration-card">
            <div class="integration-header">
                <div class="integration-icon" style="background: {{ $integration['color'] }}20; color: {{ $integration['color'] }}">
                    <i class="{{ $integration['icon'] }}"></i>
                </div>
                <div class="integration-info">
                    <h4>{{ $integration['name'] }}</h4>
                    <p>
                        @if($integration['type'] === 'email')
                            Send notifications via email
                        @else
                            Send notifications via WhatsApp
                        @endif
                    </p>
                </div>
                @if(!$integration['configured'])
                    <span class="status-badge not-configured">Not Configured</span>
                @elseif($integration['enabled'])
                    <span class="status-badge enabled">Enabled</span>
                @else
                    <span class="status-badge disabled">Disabled</span>
                @endif
            </div>

            <div class="config-section">
                <h5><i class="fas fa-cog"></i> Configuration</h5>

                @if($integration['type'] === 'email')
                    <div class="config-item">
                        <span class="config-label">SMTP Host</span>
                        <span class="config-value">{{ config('mail.mailers.smtp.host') ?: 'Not set' }}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">SMTP Port</span>
                        <span class="config-value">{{ config('mail.mailers.smtp.port') ?: 'Not set' }}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Username</span>
                        <span class="config-value">{{ config('mail.mailers.smtp.username') ?: 'Not set' }}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">From Address</span>
                        <span class="config-value">{{ config('mail.from.address') ?: 'Not set' }}</span>
                    </div>

                    <button class="btn-test" onclick="testEmail()" id="test-email-btn" {{ !$integration['configured'] ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane me-1"></i> Send Test Email
                    </button>
                    <div class="test-result" id="email-result"></div>

                    @if(!$integration['configured'])
                    <div class="env-hint">
                        <strong>Configure in .env:</strong><br>
                        <code>MAIL_HOST</code>, <code>MAIL_PORT</code>, <code>MAIL_USERNAME</code>, <code>MAIL_PASSWORD</code>
                    </div>
                    @endif
                @else
                    <div class="config-item">
                        <span class="config-label">Instance ID</span>
                        <span class="config-value {{ config('services.whatsapp.instance_id') ? '' : 'masked' }}">
                            {{ config('services.whatsapp.instance_id') ?: 'Not set' }}
                        </span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Token</span>
                        <span class="config-value masked">
                            {{ config('services.whatsapp.token') ? '••••••••' : 'Not set' }}
                        </span>
                    </div>

                    <button class="btn-test" onclick="testWhatsApp()" id="test-whatsapp-btn" {{ !$integration['configured'] ? 'disabled' : '' }}>
                        <i class="fab fa-whatsapp me-1"></i> Test Connection
                    </button>
                    <div class="test-result" id="whatsapp-result"></div>

                    @if(!$integration['configured'])
                    <div class="env-hint">
                        <strong>Configure in .env:</strong><br>
                        <code>WHATSAPP_ENABLED=true</code><br>
                        <code>WHATSAPP_INSTANCE_ID=your_instance_id</code><br>
                        <code>WHATSAPP_TOKEN=your_token</code>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="integration-card">
    <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> How to Enable/Disable</h5>
    <p class="text-muted mb-0">
        To enable or disable notifications, edit your <code>.env</code> file:<br><br>
        <strong>Email:</strong> Set <code>MAIL_ENABLED=true</code> or <code>MAIL_ENABLED=false</code><br>
        <strong>WhatsApp:</strong> Set <code>WHATSAPP_ENABLED=true</code> or <code>WHATSAPP_ENABLED=false</code><br><br>
        After changing, run: <code>php artisan config:clear</code>
    </p>
</div>

<script>
function testEmail() {
    const btn = document.getElementById('test-email-btn');
    const result = document.getElementById('email-result');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';

    fetch('{{ route("admin.integrations.test-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        result.style.display = 'block';
        result.className = 'test-result ' + (data.success ? 'success' : 'error');
        result.innerHTML = data.message;
    })
    .catch(error => {
        result.style.display = 'block';
        result.className = 'test-result error';
        result.innerHTML = 'Error: ' + error.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Test Email';
    });
}

function testWhatsApp() {
    const btn = document.getElementById('test-whatsapp-btn');
    const result = document.getElementById('whatsapp-result');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Testing...';

    fetch('{{ route("admin.integrations.test-whatsapp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        result.style.display = 'block';
        result.className = 'test-result ' + (data.success ? 'success' : 'error');
        result.innerHTML = data.message;
    })
    .catch(error => {
        result.style.display = 'block';
        result.className = 'test-result error';
        result.innerHTML = 'Error: ' + error.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fab fa-whatsapp me-1"></i> Test Connection';
    });
}
</script>
@endsection
