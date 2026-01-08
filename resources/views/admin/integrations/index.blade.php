@extends('layouts.admin')

@section('title', 'Integrations')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Integrations</h1>
        <p class="text-muted mb-0">Manage third-party service integrations</p>
    </div>
    <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Integration
    </a>
</div>
@endsection

@section('content')
<style>
    .integrations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }

    .integration-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: all 0.2s;
    }

    .integration-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .integration-header {
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .integration-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .integration-icon.whatsapp { background: #dcf8c6; color: #25d366; }
    .integration-icon.email { background: #e3f2fd; color: #1976d2; }
    .integration-icon.sms { background: #fff3e0; color: #ff9800; }
    .integration-icon.api { background: #f3e5f5; color: #9c27b0; }

    .integration-info {
        flex: 1;
        min-width: 0;
    }

    .integration-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .integration-info p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .integration-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .integration-status.active {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .integration-status.inactive {
        background: #fce4ec;
        color: #c62828;
    }

    .integration-body {
        padding: 20px;
    }

    .integration-detail {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .integration-detail:last-child {
        border-bottom: none;
    }

    .integration-detail .label {
        font-size: 13px;
        color: #64748b;
    }

    .integration-detail .value {
        font-size: 13px;
        font-weight: 500;
        color: #1e293b;
    }

    .integration-footer {
        padding: 16px 20px;
        background: #f8fafc;
        display: flex;
        gap: 8px;
    }

    .btn-integration {
        flex: 1;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-integration.view {
        background: #e3f2fd;
        color: #1565c0;
    }

    .btn-integration.view:hover {
        background: #bbdefb;
    }

    .btn-integration.edit {
        background: #fff3e0;
        color: #ef6c00;
    }

    .btn-integration.edit:hover {
        background: #ffe0b2;
    }

    .btn-integration.delete {
        background: #ffebee;
        color: #c62828;
    }

    .btn-integration.delete:hover {
        background: #ffcdd2;
    }

    .type-filter {
        display: flex;
        gap: 10px;
        margin-bottom: 24px;
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn:hover, .filter-btn.active {
        background: var(--primary-color, #2f0e13);
        border-color: var(--primary-color, #2f0e13);
        color: white;
    }

    .filter-btn .count {
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        background: rgba(0,0,0,0.1);
    }

    .filter-btn.active .count {
        background: rgba(255,255,255,0.2);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .empty-state i {
        font-size: 64px;
        color: #e2e8f0;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 20px;
    }

    @media (max-width: 640px) {
        .type-filter {
            flex-wrap: wrap;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Type Filter -->
<div class="type-filter">
    <button class="filter-btn active" data-type="all">
        All <span class="count">{{ $integrations->total() }}</span>
    </button>
    <button class="filter-btn" data-type="whatsapp">
        <i class="fab fa-whatsapp"></i> WhatsApp
    </button>
    <button class="filter-btn" data-type="email">
        <i class="fas fa-envelope"></i> Email
    </button>
    <button class="filter-btn" data-type="sms">
        <i class="fas fa-sms"></i> SMS
    </button>
    <button class="filter-btn" data-type="api">
        <i class="fas fa-code"></i> API
    </button>
</div>

@if($integrations->count() > 0)
    <div class="integrations-grid">
        @foreach($integrations as $integration)
            <div class="integration-card" data-type="{{ $integration->type }}">
                <div class="integration-header">
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
                        <h4>{{ $integration->name }}</h4>
                        <p>{{ ucfirst($integration->type) }} Integration</p>
                    </div>
                    <span class="integration-status {{ $integration->is_active ? 'active' : 'inactive' }}">
                        {{ $integration->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="integration-body">
                    <div class="integration-detail">
                        <span class="label">Provider</span>
                        <span class="value">{{ $integration->provider ?: 'Not specified' }}</span>
                    </div>
                    <div class="integration-detail">
                        <span class="label">Created</span>
                        <span class="value">{{ $integration->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="integration-detail">
                        <span class="label">Last Updated</span>
                        <span class="value">{{ $integration->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="integration-footer">
                    <a href="{{ route('admin.integrations.show', $integration) }}" class="btn-integration view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('admin.integrations.edit', $integration) }}" class="btn-integration edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.integrations.destroy', $integration) }}" method="POST" style="flex: 1;"
                          onsubmit="return confirm('Are you sure you want to delete this integration?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-integration delete" style="width: 100%;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{ $integrations->links() }}
@else
    <div class="empty-state">
        <i class="fas fa-plug"></i>
        <h4>No Integrations Yet</h4>
        <p>Add integrations to connect with external services like WhatsApp, Email, SMS, and more.</p>
        <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add First Integration
        </a>
    </div>
@endif

<script>
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;

            // Update active state
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter cards
            document.querySelectorAll('.integration-card').forEach(card => {
                if (type === 'all' || card.dataset.type === type) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection
