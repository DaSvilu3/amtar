@extends('layouts.admin')

@section('title', 'Two-Factor Authentication - Amtar Admin')

@section('content')
<div class="page-title">
    <h1>Two-Factor Authentication</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active">Two-Factor Authentication</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle p-3 {{ $enabled ? 'bg-success' : 'bg-secondary' }} bg-opacity-10">
                    <i class="fas fa-shield-alt fa-2x {{ $enabled ? 'text-success' : 'text-secondary' }}"></i>
                </div>
                <div>
                    <h4 class="mb-1">Two-Factor Authentication</h4>
                    <span class="badge {{ $enabled ? 'bg-success' : 'bg-secondary' }}">
                        {{ $enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>

            <p class="text-muted mb-4">
                Two-factor authentication adds an extra layer of security to your account. When enabled,
                you'll need to enter a code from your authenticator app in addition to your password when signing in.
            </p>

            @if($enabled)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Two-factor authentication is currently <strong>enabled</strong> on your account.
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-key me-2"></i>Recovery Codes</h6>
                                <p class="card-text text-muted small">
                                    You have {{ count($recoveryCodes) }} recovery codes remaining.
                                    Store these codes in a safe place - they can be used to access your account if you lose your authenticator device.
                                </p>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#regenerateModal">
                                    <i class="fas fa-sync me-1"></i>Regenerate Codes
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-times-circle me-2"></i>Disable 2FA</h6>
                                <p class="card-text text-muted small">
                                    If you no longer want to use two-factor authentication, you can disable it.
                                    This will make your account less secure.
                                </p>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#disableModal">
                                    <i class="fas fa-power-off me-1"></i>Disable 2FA
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Two-factor authentication is currently <strong>disabled</strong>. We recommend enabling it for better security.
                </div>

                <a href="{{ route('admin.two-factor.enable') }}" class="btn btn-primary">
                    <i class="fas fa-shield-alt me-2"></i>Enable Two-Factor Authentication
                </a>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="dashboard-card">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-muted"></i>How it works</h5>
            <ol class="text-muted small ps-3 mb-0">
                <li class="mb-2">Download an authenticator app like Google Authenticator or Authy</li>
                <li class="mb-2">Scan the QR code or enter the secret key</li>
                <li class="mb-2">Enter the 6-digit code from the app to verify</li>
                <li class="mb-2">Save your recovery codes in a safe place</li>
                <li>Use the code from your app each time you log in</li>
            </ol>
        </div>
    </div>
</div>

<!-- Disable Modal -->
<div class="modal fade" id="disableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.two-factor.disable') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to disable two-factor authentication? This will make your account less secure.</p>
                    <div class="mb-3">
                        <label class="form-label">Confirm your password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disable 2FA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
<div class="modal fade" id="regenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.two-factor.regenerate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Regenerate Recovery Codes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will invalidate all existing recovery codes.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm your password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Regenerate Codes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
