<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Amtar Admin Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Poppins & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2f0e13;
            --secondary-color: #f3c887;
            --text-light: #ffffff;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --bg-light: #f8f9fa;
            --bg-dark: #1a0508;
            --hover-color: #4a1a1f;
            --shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: #ffffff;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            border-right: 1px solid #e0e0e0;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            background: #ffffff;
        }
        
        .sidebar-logo {
            width: 120px;
            height: auto;
            transition: all 0.3s ease;
            /* filter: brightness(0) invert(1); */
        }
        
        .sidebar.collapsed .sidebar-logo {
            width: 40px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
            overflow-y: auto;
            height: calc(100vh - 100px);
        }
        
        .menu-section {
            margin-bottom: 20px;
        }

        .menu-section:first-child {
            margin-top: 10px;
        }

        .menu-section-title {
            color: #a0aec0;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 20px 20px 10px 20px;
            margin-bottom: 5px;
        }
        
        .sidebar.collapsed .menu-section-title {
            display: none;
        }
        
        .menu-item {
            position: relative;
        }
        
        .menu-link {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            margin: 2px 10px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.25s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .menu-link:hover {
            background: #f7fafc;
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .menu-link.active {
            background: #f0f4f8;
            border-left: 3px solid var(--secondary-color);
            color: var(--primary-color);
        }
        
        .menu-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(243, 200, 135, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .menu-link:hover::before {
            left: 100%;
        }
        
        .menu-icon {
            font-size: 16px;
            width: 24px;
            margin-right: 12px;
            color: #718096;
            text-align: center;
        }

        .menu-link:hover .menu-icon,
        .menu-link.active .menu-icon {
            color: var(--secondary-color);
        }
        
        .sidebar.collapsed .menu-icon {
            margin-right: 0;
        }
        
        .menu-text {
            font-size: 13px;
            font-weight: 400;
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .menu-text {
            display: none;
        }
        
        .menu-badge {
            margin-left: auto;
            background: var(--secondary-color);
            color: var(--primary-color);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .sidebar.collapsed .menu-badge {
            display: none;
        }
        
        /* Submenu Styles */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0,0,0,0.2);
        }
        
        .submenu.show {
            max-height: 500px;
        }
        
        .submenu .menu-link {
            padding-left: 50px;
            font-size: 13px;
        }
        
        .menu-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }
        
        .menu-arrow.rotate {
            transform: rotate(90deg);
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            z-index: 999;
            transition: left 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 30px;
        }
        
        .sidebar.collapsed ~ .main-wrapper .header {
            left: var(--sidebar-collapsed-width);
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .toggle-sidebar:hover {
            transform: scale(1.1);
        }
        
        .header-search {
            flex: 1;
            max-width: 400px;
            margin: 0 30px;
        }
        
        .header-search input {
            width: 100%;
            padding: 10px 20px;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .header-search input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(243, 200, 135, 0.1);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }
        
        .header-action-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .header-action-btn:hover {
            color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 15px;
            background: var(--bg-light);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-profile:hover {
            background: var(--secondary-color);
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--primary-color);
        }
        
        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }
        
        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .main-content {
            padding: calc(var(--header-height) + 30px) 30px 30px;
            min-height: 100vh;
        }
        
        /* Page Title */
        .page-title {
            margin-bottom: 30px;
        }
        
        .page-title h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            list-style: none;
        }

        .breadcrumb-item {
            font-size: 14px;
            color: #718096;
            display: flex;
            align-items: center;
        }

        .breadcrumb-item a {
            color: #4a5568;
            text-decoration: none;
            transition: color 0.2s ease;
            font-weight: 400;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            padding: 0 10px;
            color: #cbd5e0;
            font-weight: 300;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        /* Card Styles */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-color: var(--secondary-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .header {
                left: 0;
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .header-search {
                display: none;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        /* Pagination Styles */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            color: var(--primary-color);
            border: 1px solid #dee2e6;
            padding: 8px 14px;
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .pagination .page-item .page-link:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/logo-no-backgreound.png" alt="Amtar Logo" class="sidebar-logo">
        </div>
        
        <nav class="sidebar-menu">
            @foreach($navigationSections as $section)
                <div class="menu-section">
                    @if($section['title'])
                        <div class="menu-section-title">{{ $section['title'] }}</div>
                    @endif

                    @foreach($section['items'] as $item)
                        <div class="menu-item">
                            <a href="{{ route($item['route']) }}" class="menu-link {{ request()->is($item['activePattern']) ? 'active' : '' }}">
                                <i class="fas {{ $item['icon'] }} menu-icon"></i>
                                <span class="menu-text">{{ $item['label'] }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </nav>
    </aside>
    
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <header class="header">
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="header-search">
                <input type="text" placeholder="Search projects, clients, tasks...">
            </div>
            
            <div class="header-actions">
                <button class="header-action-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </button>
                
                <button class="header-action-btn">
                    <i class="fas fa-envelope"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <div class="user-profile dropdown" data-bs-toggle="dropdown">
                    <div class="user-avatar">{{ auth()->check() ? auth()->user()->name[0] : 'A' }}</div>
                    <span class="user-name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>

                    <ul class="dropdown-menu dropdown-menu-end">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('admin.users.show', auth()->id()) }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                        @endauth
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
        
        // Toggle Submenu
        function toggleSubmenu(event, menuId) {
            event.preventDefault();
            const submenu = document.getElementById(menuId);
            const arrow = event.currentTarget.querySelector('.menu-arrow');
            
            submenu.classList.toggle('show');
            arrow.classList.toggle('rotate');
        }
        
        // Load sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>