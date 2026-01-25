<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Amtar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #2f0e13;
            --secondary-color: #f3c887;
        }
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a0508 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .auth-logo {
            height: 40px;
            margin-bottom: 24px;
        }
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background: #4a1a22;
            border-color: #4a1a22;
        }
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 12px 20px;
        }
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background: transparent;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <img src="/logo-no-backgreound.png" alt="Amtar" class="auth-logo">
            <h4 class="mb-2">Two-Factor Authentication</h4>
            <p class="text-muted small">Enter the code from your authenticator app</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#code-tab">
                    <i class="fas fa-mobile-alt me-2"></i>Authentication Code
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#recovery-tab">
                    <i class="fas fa-key me-2"></i>Recovery Code
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Code Tab -->
            <div class="tab-pane fade show active" id="code-tab">
                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">6-digit code</label>
                        <input type="text"
                               name="code"
                               class="form-control form-control-lg text-center"
                               placeholder="000000"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               autofocus>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check me-2"></i>Verify
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recovery Tab -->
            <div class="tab-pane fade" id="recovery-tab">
                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Recovery code</label>
                        <input type="text"
                               name="recovery_code"
                               class="form-control"
                               placeholder="Enter a recovery code">
                        <small class="text-muted">Use one of your saved recovery codes if you can't access your authenticator app.</small>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-key me-2"></i>Verify with Recovery Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-muted small">
                <i class="fas fa-arrow-left me-1"></i>Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
