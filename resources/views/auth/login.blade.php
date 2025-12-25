<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amtar Consultancy</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2f0e13;
            --primary-dark: #1a0508;
            --accent: #f3c887;
            --accent-light: #f8ddb0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: var(--bg-light);
        }

        .login-page {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Panel - Branding */
        .brand-panel {
            flex: 0 0 45%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(243, 200, 135, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -10%;
            left: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(243, 200, 135, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-header {
            padding: 40px;
        }

        .brand-logo {
            height: 48px;
            filter: brightness(0) invert(1);
        }

        .brand-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            z-index: 1;
        }

        .brand-title {
            font-family: 'Poppins', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .brand-title span {
            color: var(--accent);
        }

        .brand-description {
            font-size: 16px;
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            max-width: 400px;
            margin-bottom: 48px;
        }

        .brand-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 420px;
        }

        .stat-item {
            text-align: left;
        }

        .stat-value {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .brand-footer {
            padding: 40px 60px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .brand-services {
            display: flex;
            gap: 32px;
        }

        .service-tag {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }

        .service-tag i {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--accent);
            font-size: 14px;
        }

        /* Right Panel - Login Form */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: white;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .mobile-logo {
            display: none;
            margin-bottom: 32px;
        }

        .mobile-logo img {
            height: 40px;
        }

        .form-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 15px;
            color: var(--text-muted);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-danger i {
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            padding-left: 48px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--bg-light);
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 4px rgba(243, 200, 135, 0.15);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
        }

        .form-input:focus + .input-icon {
            color: var(--primary);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: var(--text-dark);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-wrapper input {
            display: none;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .checkbox-custom i {
            font-size: 10px;
            color: white;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }

        .checkbox-wrapper input:checked + .checkbox-custom {
            background: var(--primary);
            border-color: var(--primary);
        }

        .checkbox-wrapper input:checked + .checkbox-custom i {
            opacity: 1;
            transform: scale(1);
        }

        .checkbox-label {
            font-size: 14px;
            color: var(--text-muted);
        }

        .forgot-link {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--accent);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(47, 14, 19, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 32px;
            text-align: center;
        }

        .form-footer-text {
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-footer-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer-link:hover {
            text-decoration: underline;
        }

        .copyright {
            margin-top: 48px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .brand-panel {
                flex: 0 0 40%;
            }

            .brand-content {
                padding: 40px;
            }

            .brand-title {
                font-size: 36px;
            }

            .brand-footer {
                padding: 32px 40px;
            }
        }

        @media (max-width: 768px) {
            .brand-panel {
                display: none;
            }

            .form-panel {
                padding: 24px;
            }

            .mobile-logo {
                display: block;
            }

            .form-header {
                margin-bottom: 32px;
            }

            .form-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- Left Panel - Branding -->
        <div class="brand-panel">
            <div class="brand-header">
                <img src="/logo-no-backgreound.png" alt="Amtar" class="brand-logo">
            </div>

            <div class="brand-content">
                <h1 class="brand-title">
                    Professional<br>
                    <span>Engineering</span><br>
                    Consultancy
                </h1>
                <p class="brand-description">
                    Delivering excellence in engineering design, project management, and construction supervision across the Gulf region since 2010.
                </p>

                <div class="brand-stats">
                    <div class="stat-item">
                        <div class="stat-value">150+</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">50+</div>
                        <div class="stat-label">Clients</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">15+</div>
                        <div class="stat-label">Years</div>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                <div class="brand-services">
                    <div class="service-tag">
                        <i class="fas fa-drafting-compass"></i>
                        <span>Interior Design</span>
                    </div>
                    <div class="service-tag">
                        <i class="fas fa-hard-hat"></i>
                        <span>Engineering</span>
                    </div>
                    <div class="service-tag">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Supervision</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="form-panel">
            <div class="form-container">
                <div class="mobile-logo">
                    <img src="/logo-no-backgreound.png" alt="Amtar">
                </div>

                <div class="form-header">
                    <h2 class="form-title">Welcome back</h2>
                    <p class="form-subtitle">Sign in to access your dashboard</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email"
                                   class="form-input"
                                   name="email"
                                   placeholder="name@company.com"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input type="password"
                                   class="form-input"
                                   name="password"
                                   id="password"
                                   placeholder="Enter your password"
                                   required>
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember">
                            <span class="checkbox-custom"><i class="fas fa-check"></i></span>
                            <span class="checkbox-label">Keep me signed in</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        Sign In
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Need an account? <a href="#" class="form-footer-link">Contact Administrator</a>
                    </p>
                </div>

                <div class="copyright">
                    &copy; {{ date('Y') }} Amtar Consultancy. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
