@extends('layouts.admin')

@section('title', 'Enable Two-Factor Authentication - Amtar Admin')

@section('content')
<div class="page-title">
    <h1>Enable Two-Factor Authentication</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.two-factor.show') }}">Two-Factor Authentication</a></li>
            <li class="breadcrumb-item active">Enable</li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="dashboard-card">
            <h4 class="mb-4">Set Up Your Authenticator</h4>

            <div class="text-center mb-4">
                <div class="bg-white p-3 d-inline-block rounded border">
                    {!! $qrCode !!}
                </div>
            </div>

            <div class="alert alert-light border mb-4">
                <h6 class="mb-2">Can't scan the code?</h6>
                <p class="mb-2 small text-muted">Enter this secret key manually in your authenticator app:</p>
                <code class="d-block p-2 bg-dark text-white rounded text-center" style="word-break: break-all;">
                    {{ $secret }}
                </code>
            </div>

            <form action="{{ route('admin.two-factor.confirm') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Enter the 6-digit code from your authenticator app</label>
                    <input type="text"
                           name="code"
                           class="form-control form-control-lg text-center @error('code') is-invalid @enderror"
                           placeholder="000000"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required
                           autofocus>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check me-2"></i>Verify & Enable
                    </button>
                    <a href="{{ route('admin.two-factor.show') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
