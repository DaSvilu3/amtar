<div class="coming-soon-container">
    <style>
        .coming-soon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - var(--header-height) - 60px);
            padding: 30px;
        }
        
        .coming-soon-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .coming-soon-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(243, 200, 135, 0.1), transparent);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }
        
        .coming-soon-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, var(--secondary-color), #ffdb9e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(243, 200, 135, 0.4);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(243, 200, 135, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(243, 200, 135, 0);
            }
        }
        
        .coming-soon-icon i {
            font-size: 48px;
            color: white;
        }
        
        .coming-soon-title {
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Poppins', sans-serif;
        }
        
        .coming-soon-subtitle {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .coming-soon-features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(243, 200, 135, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .feature-item:hover .feature-icon {
            background: var(--secondary-color);
            transform: translateY(-5px);
        }
        
        .feature-item:hover .feature-icon i {
            color: white;
        }
        
        .feature-icon i {
            font-size: 20px;
            color: var(--secondary-color);
            transition: color 0.3s ease;
        }
        
        .feature-text {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .progress-indicator {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .progress-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .progress-bar-wrapper {
            background: #f0f0f0;
            height: 6px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color), #ffdb9e);
            border-radius: 10px;
            animation: progressAnimation 2s ease-out;
        }
        
        @keyframes progressAnimation {
            from {
                width: 0%;
            }
        }
    </style>
    
    <div class="coming-soon-card">
        <div class="coming-soon-icon">
            <i class="fas fa-rocket"></i>
        </div>
        
        <h2 class="coming-soon-title">{{ $title ?? 'Coming Soon' }}</h2>
        
        <p class="coming-soon-subtitle">
            {{ $message ?? 'We\'re working hard to bring you this feature. It will be available soon!' }}
        </p>

        @php($progress = 0)
        
        <div class="coming-soon-features">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="feature-text">Analytics</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <span class="feature-text">Advanced Settings</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-magic"></i>
                </div>
                <span class="feature-text">Smart Features</span>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <span class="feature-text">Enhanced Security</span>
            </div>
        </div>
        
        <div class="progress-indicator">
            <p class="progress-text">Development Progress</p>
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" style="width: {{ $progress ?? 65 }}%;"></div>
            </div>
        </div>
    </div>
</div>