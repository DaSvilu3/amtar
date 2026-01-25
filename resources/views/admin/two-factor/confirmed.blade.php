@extends('layouts.admin')

@section('title', 'Two-Factor Authentication Enabled - Amtar Admin')

@section('content')
<div class="page-title">
    <h1>{{ isset($regenerated) ? 'Recovery Codes Regenerated' : 'Two-Factor Authentication Enabled' }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.two-factor.show') }}">Two-Factor Authentication</a></li>
            <li class="breadcrumb-item active">{{ isset($regenerated) ? 'Recovery Codes' : 'Enabled' }}</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="text-center mb-4">
                <div class="rounded-circle bg-success bg-opacity-10 p-4 d-inline-block mb-3">
                    <i class="fas fa-check-circle fa-3x text-success"></i>
                </div>
                <h4>{{ isset($regenerated) ? 'New Recovery Codes Generated' : 'Two-Factor Authentication Enabled!' }}</h4>
                <p class="text-muted">
                    {{ isset($regenerated)
                        ? 'Your old recovery codes have been invalidated. Please save these new codes.'
                        : 'Your account is now protected with an additional layer of security.'
                    }}
                </p>
            </div>

            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Save Your Recovery Codes</h6>
                <p class="mb-0 small">
                    Store these recovery codes in a safe place. They can be used to recover access to your account
                    if you lose your authenticator device. Each code can only be used once.
                </p>
            </div>

            <div class="bg-light p-3 rounded mb-4" id="recovery-codes">
                <div class="row g-2">
                    @foreach($recoveryCodes as $code)
                        <div class="col-6">
                            <code class="d-block text-center p-2 bg-white rounded border">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="copyRecoveryCodes()">
                    <i class="fas fa-copy me-2"></i>Copy Codes
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="downloadRecoveryCodes()">
                    <i class="fas fa-download me-2"></i>Download Codes
                </button>
                <a href="{{ route('admin.two-factor.show') }}" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Done
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyRecoveryCodes() {
    const codes = @json($recoveryCodes);
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes copied to clipboard!');
    });
}

function downloadRecoveryCodes() {
    const codes = @json($recoveryCodes);
    const text = "AMTAR Two-Factor Authentication Recovery Codes\n" +
                 "Generated: " + new Date().toLocaleString() + "\n" +
                 "================================================\n\n" +
                 codes.join('\n') + "\n\n" +
                 "Keep these codes in a safe place. Each code can only be used once.";

    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'amtar-recovery-codes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
