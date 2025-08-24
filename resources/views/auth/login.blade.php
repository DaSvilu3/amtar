<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amtar Consultancy</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2f0e13;
            --secondary-color: #f3c887;
            --text-light: #ffffff;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }
        
        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        /* Left Side - Image */
        .login-image-section {
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, rgba(47, 14, 19, 0.9), rgba(47, 14, 19, 0.7)), 
                        url('/theme.jpg') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px;
            color: white;
        }
        
        .login-image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 80%, rgba(243, 200, 135, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(243, 200, 135, 0.2) 0%, transparent 50%);
        }
        
        .brand-section {
            position: relative;
            z-index: 1;
            text-align: center;
            animation: fadeInUp 1s ease;
        }
        
        .brand-logo {
            width: 150px;
            height: auto;
            margin-bottom: 30px;
            /* filter: brightness(0) invert(1); */
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .brand-title {
            font-size: 48px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 15px;
            background: linear-gradient(45deg, var(--text-light), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand-subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .brand-features {
            display: flex;
            gap: 40px;
            margin-top: 50px;
        }
        
        .feature {
            text-align: center;
            animation: fadeIn 1.5s ease;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .feature:hover .feature-icon {
            background: var(--secondary-color);
            transform: scale(1.1);
        }
        
        .feature-icon i {
            font-size: 24px;
            color: var(--secondary-color);
            transition: color 0.3s ease;
        }
        
        .feature:hover .feature-icon i {
            color: var(--primary-color);
        }
        
        .feature-text {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Right Side - Login Form */
        .login-form-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
        }
        
        .login-form-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(243, 200, 135, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .login-form-wrapper {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            animation: fadeInRight 1s ease;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-family: 'Poppins', sans-serif;
        }
        
        .login-subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(243, 200, 135, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 18px;
            z-index: 1;
        }
        
        .form-control.with-icon {
            padding-right: 45px;
        }
        
        .form-check {
            margin-bottom: 25px;
        }
        
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .form-check-label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .forgot-password {
            float: right;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-color);
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-color));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(243, 200, 135, 0.3), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(47, 14, 19, 0.3);
        }
        
        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider-text {
            background: white;
            padding: 0 15px;
            color: #6c757d;
            font-size: 13px;
            position: relative;
        }
        
        .social-login {
            display: flex;
            gap: 15px;
        }
        
        .social-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
            text-decoration: none;
        }
        
        .social-btn:hover {
            border-color: var(--secondary-color);
            background: rgba(243, 200, 135, 0.1);
            transform: translateY(-2px);
        }
        
        .signup-link {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .signup-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: var(--primary-color);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: fadeInUp 0.5s ease;
        }
        
        .alert-danger {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .login-image-section {
                display: none;
            }
            
            .login-form-section {
                flex: 1;
            }
        }
        
        @media (max-width: 576px) {
            .login-form-section {
                padding: 20px;
            }
            
            .login-card {
                padding: 30px 20px;
            }
            
            .login-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Image Section -->
        <div class="login-image-section">
            <div class="brand-section">
                <img src="/logo.jpg" alt="Amtar Logo" class="brand-logo">
                <h1 class="brand-title">AMTAR</h1>
                <p class="brand-subtitle">Engineering Consultancy Excellence</p>
                
                <div class="brand-features">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div class="feature-text">Engineering</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="feature-text">Design</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="feature-text">Construction</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-form-section">
            <div class="login-form-wrapper">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="login-title">Welcome Back</h2>
                        <p class="login-subtitle">Please login to your account</p>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <input type="email" 
                                       class="form-control with-icon" 
                                       name="email" 
                                       placeholder="Enter your email"
                                       value="{{ old('email') }}"
                                       required>
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control with-icon" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i> Login to Dashboard
                        </button>
                    </form>
                    
                    <div class="divider">
                        <span class="divider-text">OR</span>
                    </div>
                    
                    <div class="social-login">
                        <a href="#" class="social-btn">
                            <i class="fab fa-google"></i>
                            Google
                        </a>
                        <a href="#" class="social-btn">
                            <i class="fab fa-microsoft"></i>
                            Microsoft
                        </a>
                    </div>
                    
                    <div class="signup-link">
                        Don't have an account? <a href="#">Contact Administrator</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>