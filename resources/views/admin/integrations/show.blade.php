@extends('layouts.admin')

@section('title', 'View Integration')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">{{ $integration->name }}</h1>
        <p class="text-muted mb-0">Integration details and configuration</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.integrations.edit', $integration) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>
@endsection

@section('content')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .detail-card.full-width {
        grid-column: span 2;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        flex: 1;
    }

    .card-header i {
        font-size: 20px;
    }

    .card-body {
        padding: 24px;
    }

    .integration-hero {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px;
    }

    .integration-icon {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }

    .integration-icon.whatsapp { background: #dcf8c6; color: #25d366; }
    .integration-icon.email { background: #e3f2fd; color: #1976d2; }
    .integration-icon.sms { background: #fff3e0; color: #ff9800; }
    .integration-icon.api { background: #f3e5f5; color: #9c27b0; }

    .integration-info h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px;
    }

    .integration-meta {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-badge.whatsapp { background: #dcf8c6; color: #25d366; }
    .type-badge.email { background: #e3f2fd; color: #1976d2; }
    .type-badge.sms { background: #fff3e0; color: #ff9800; }
    .type-badge.api { background: #f3e5f5; color: #9c27b0; }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.active { background: #e8f5e9; color: #2e7d32; }
    .status-badge.inactive { background: #fce4ec; color: #c62828; }

    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 14px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-label i {
        width: 16px;
        text-align: center;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
    }

    .config-item {
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .config-item:last-child {
        margin-bottom: 0;
    }

    .config-key {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .config-value {
        font-size: 14px;
        color: #1e293b;
        font-family: 'Courier New', monospace;
        word-break: break-all;
    }

    .config-value.masked {
        color: #94a3b8;
    }

    .empty-config {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }

    .empty-config i {
        font-size: 32px;
        color: #e2e8f0;
        margin-bottom: 12px;
    }

    .test-section {
        padding: 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }

    .btn-test {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: var(--primary-color, #2f0e13);
        border: none;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-test:hover {
        background: #1a0508;
    }

    .btn-test:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .test-result {
        margin-top: 16px;
        padding: 16px;
        border-radius: 8px;
        font-size: 14px;
        display: none;
    }

    .test-result.success {
        background: #e8f5e9;
        color: #2e7d32;
        display: block;
    }

    .test-result.error {
        background: #ffebee;
        color: #c62828;
        display: block;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        padding: 24px;
        border-top: 1px solid #f1f5f9;
    }

    .btn-action {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-action.edit {
        background: #e3f2fd;
        color: #1565c0;
    }

    .btn-action.edit:hover {
        background: #bbdefb;
    }

    .btn-action.delete {
        background: #ffebee;
        color: #c62828;
    }

    .btn-action.delete:hover {
        background: #ffcdd2;
    }

    @media (max-width: 1024px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-card.full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="detail-grid">
    <!-- Hero Section -->
    <div class="detail-card full-width">
        <div class="integration-hero">
            <div class="integration-icon {{ $integration->type }}">
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
            <div class="integration-info">
                <h2>{{ $integration->name }}</h2>
                <div class="integration-meta">
                    <span class="type-badge {{ $integration->type }}">{{ ucfirst($integration->type) }}</span>
                    <span class="status-badge {{ $integration->is_active ? 'active' : 'inactive' }}">
                        {{ $integration->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="detail-card">
        <div class="card-header">
            <i class="fas fa-info-circle text-primary"></i>
            <h4>Details</h4>
        </div>
        <div class="card-body">
            <ul class="detail-list">
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-tag"></i> Name</span>
                    <span class="detail-value">{{ $integration->name }}</span>
                </li>
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-plug"></i> Type</span>
                    <span class="detail-value">{{ ucfirst($integration->type) }}</span>
                </li>
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-building"></i> Provider</span>
                    <span class="detail-value">{{ $integration->provider ?: 'Not specified' }}</span>
                </li>
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-toggle-on"></i> Status</span>
                    <span class="detail-value">
                        <span class="status-badge {{ $integration->is_active ? 'active' : 'inactive' }}">
                            {{ $integration->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </span>
                </li>
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-calendar"></i> Created</span>
                    <span class="detail-value">{{ $integration->created_at->format('M d, Y H:i') }}</span>
                </li>
                <li class="detail-item">
                    <span class="detail-label"><i class="fas fa-clock"></i> Last Updated</span>
                    <span class="detail-value">{{ $integration->updated_at->diffForHumans() }}</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Configuration -->
    <div class="detail-card">
        <div class="card-header">
            <i class="fas fa-cog text-warning"></i>
            <h4>Configuration</h4>
        </div>
        <div class="card-body">
            @php
                $config = is_string($integration->config) ? json_decode($integration->config, true) : ($integration->config ?? []);
            @endphp

            @if(!empty($config))
                @foreach($config as $key => $value)
                    <div class="config-item">
                        <div class="config-key">{{ str_replace('_', ' ', $key) }}</div>
                        <div class="config-value {{ in_array($key, ['api_key', 'auth_token', 'api_secret', 'password']) ? 'masked' : '' }}">
                            @if(in_array($key, ['api_key', 'auth_token', 'api_secret', 'password']))
                                {{ str_repeat('*', min(strlen($value), 20)) }}
                            @else
                                {{ $value ?: 'Not set' }}
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-config">
                    <i class="fas fa-cog"></i>
                    <p>No configuration set for this integration.</p>
                </div>
            @endif
        </div>

        <div class="test-section">
            <button type="button" class="btn-test" id="testBtn" {{ !$integration->is_active ? 'disabled' : '' }}>
                <i class="fas fa-bolt"></i> Test Connection
            </button>
            <div class="test-result" id="testResult"></div>
        </div>
    </div>

    <!-- Actions -->
    <div class="detail-card full-width">
        <div class="action-buttons">
            <a href="{{ route('admin.integrations.edit', $integration) }}" class="btn-action edit">
                <i class="fas fa-edit"></i> Edit Integration
            </a>
            <form action="{{ route('admin.integrations.destroy', $integration) }}" method="POST" style="flex: 1;"
                  onsubmit="return confirm('Are you sure you want to delete this integration?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action delete" style="width: 100%;">
                    <i class="fas fa-trash"></i> Delete Integration
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('testBtn').addEventListener('click', function() {
        const btn = this;
        const result = document.getElementById('testResult');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';

        // Simulate connection test
        setTimeout(() => {
            const success = Math.random() > 0.3; // 70% success rate for demo

            if (success) {
                result.className = 'test-result success';
                result.innerHTML = '<i class="fas fa-check-circle"></i> Connection successful! The integration is working properly.';
            } else {
                result.className = 'test-result error';
                result.innerHTML = '<i class="fas fa-times-circle"></i> Connection failed. Please check your configuration settings.';
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt"></i> Test Connection';
        }, 2000);
    });
</script>
@endsection
