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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2f0e13;
            --primary-light: #4a1a22;
            --secondary-color: #f3c887;
            --secondary-light: #f8ddb0;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --header-height: 64px;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #1a0508 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }

        /* Logo */
        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            min-height: 72px;
        }

        .sidebar-logo {
            height: 36px;
            width: auto;
            filter: brightness(0) invert(1);
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .sidebar-logo { height: 26px; }

        /* User Section */
        .sidebar-user {
            padding: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar.collapsed .sidebar-user {
            padding: 12px;
            justify-content: center;
        }

        .user-avatar-lg {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 10px;
            background: var(--secondary-color);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .sidebar.collapsed .user-avatar-lg {
            width: 38px;
            height: 38px;
            min-width: 38px;
        }

        .user-details { overflow: hidden; }
        .sidebar.collapsed .user-details { display: none; }

        .user-name-lg {
            font-size: 14px;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 10px;
            color: var(--primary-color);
            background: var(--secondary-color);
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 0;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 4px;
        }

        .nav-group { margin-bottom: 4px; }

        .nav-group-title {
            color: rgba(255,255,255,0.35);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 16px 8px;
            display: flex;
            align-items: center;
        }

        .nav-group-title span { margin-right: 10px; }

        .nav-group-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.08);
        }

        .sidebar.collapsed .nav-group-title {
            padding: 12px 0;
            justify-content: center;
        }

        .sidebar.collapsed .nav-group-title span,
        .sidebar.collapsed .nav-group-title::after { display: none; }

        /* Nav Items */
        .nav-item { padding: 0 8px; margin-bottom: 2px; }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 11px 12px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.15s ease;
            position: relative;
            gap: 12px;
        }

        .sidebar.collapsed .nav-link {
            padding: 11px;
            justify-content: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .nav-link.active {
            background: rgba(243, 200, 135, 0.12);
            color: var(--secondary-color);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 18px;
            background: var(--secondary-color);
            border-radius: 0 3px 3px 0;
        }

        .sidebar.collapsed .nav-link.active::before { display: none; }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            opacity: 0.85;
        }

        .nav-link.active .nav-icon,
        .nav-link:hover .nav-icon { opacity: 1; }

        .nav-label {
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-label { display: none; }

        .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.15);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 7px;
            border-radius: 6px;
            min-width: 20px;
            text-align: center;
        }

        .nav-badge.badge-warning { background: var(--warning-color); }
        .nav-badge.badge-danger { background: var(--danger-color); }
        .nav-badge.badge-success { background: var(--success-color); }

        .sidebar.collapsed .nav-badge { display: none; }

        /* Collapsed Tooltips */
        .sidebar.collapsed .nav-link { position: relative; }

        .sidebar.collapsed .nav-link::after {
            content: attr(data-title);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.15s ease;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .sidebar.collapsed .nav-link:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* Quick Action Button */
        .sidebar-action {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .btn-quick-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11px;
            background: var(--secondary-color);
            border: none;
            border-radius: 8px;
            color: var(--primary-color);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-quick-action:hover {
            background: var(--secondary-light);
            color: var(--primary-color);
            transform: translateY(-1px);
        }

        .sidebar.collapsed .btn-quick-action span { display: none; }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .sidebar.collapsed .btn-logout span { display: none; }

        /* ===== HEADER ===== */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid var(--border-color);
            z-index: 999;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
        }

        .sidebar.collapsed ~ .main-wrapper .header {
            left: var(--sidebar-collapsed-width);
        }

        .btn-toggle {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-toggle:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .header-search {
            flex: 1;
            max-width: 380px;
            position: relative;
        }

        .header-search i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .header-search input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            background: var(--bg-light);
            transition: all 0.15s ease;
        }

        .header-search input:focus {
            outline: none;
            border-color: var(--secondary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(243, 200, 135, 0.12);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }

        .btn-header {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            border-radius: 10px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-header:hover {
            background: var(--bg-light);
            color: var(--primary-color);
        }

        .btn-header .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            background: var(--danger-color);
            color: white;
            font-size: 10px;
            font-weight: 600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-divider {
            width: 1px;
            height: 28px;
            background: var(--border-color);
            margin: 0 8px;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .header-user:hover {
            border-color: var(--secondary-color);
            background: white;
        }

        .header-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .header-user-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--primary-color);
        }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed ~ .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        .main-content {
            padding: calc(var(--header-height) + 24px) 24px 24px;
            min-height: 100vh;
        }

        /* Page Title */
        .page-title { margin-bottom: 24px; }

        .page-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 13px;
            list-style: none;
            display: flex;
            align-items: center;
        }

        .breadcrumb-item { color: var(--text-muted); }

        .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .breadcrumb-item a:hover { color: var(--primary-color); }

        .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            padding: 0 8px;
            color: #cbd5e1;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Dashboard Card */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            transition: all 0.15s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .header { left: 0; }
            .main-wrapper { margin-left: 0; }
            .header-search { display: none; }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in { animation: fadeIn 0.3s ease; }

        /* Pagination */
        .pagination .page-item .page-link {
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            padding: 8px 14px;
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .pagination .page-item .page-link:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: var(--text-muted);
            background-color: var(--bg-light);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/logo-no-backgreound.png" alt="Amtar" class="sidebar-logo">
        </div>

        <div class="sidebar-user">
            <div class="user-avatar-lg">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
            <div class="user-details">
                <div class="user-name-lg">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
                <span class="user-role-badge">
                    @if(auth()->check())
                        {{ auth()->user()->roles->first()?->name ?? 'User' }}
                    @else
                        Admin
                    @endif
                </span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @foreach($navigationSections as $section)
                <div class="nav-group">
                    @if($section['title'])
                        <div class="nav-group-title"><span>{{ $section['title'] }}</span></div>
                    @endif

                    @foreach($section['items'] as $item)
                        <div class="nav-item">
                            <a href="{{ route($item['route']) }}"
                               class="nav-link {{ request()->is($item['activePattern']) ? 'active' : '' }}"
                               data-title="{{ $item['label'] }}">
                                <span class="nav-icon"><i class="fas {{ $item['icon'] }}"></i></span>
                                <span class="nav-label">{{ $item['label'] }}</span>
                                @if(isset($item['badge']))
                                    <span class="nav-badge {{ $item['badgeClass'] ?? '' }}">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </nav>

        @if(auth()->check() && (auth()->user()->hasRole('administrator') || auth()->user()->hasRole('project-manager')))
        <div class="sidebar-action">
            <a href="{{ route('admin.projects.create') }}" class="btn-quick-action">
                <i class="fas fa-plus"></i>
                <span>New Project</span>
            </a>
        </div>
        @endif

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="header">
            <button class="btn-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <div class="header-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search projects, clients, tasks...">
            </div>

            <div class="header-actions">
                <button class="btn-header" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="badge">5</span>
                </button>

                <button class="btn-header" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="badge">3</span>
                </button>

                <div class="header-divider"></div>

                <div class="header-user dropdown" data-bs-toggle="dropdown">
                    <div class="header-user-avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
                    <span class="header-user-name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>

                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('admin.users.show', auth()->id()) }}"><i class="fas fa-user me-2 text-muted"></i>My Profile</a></li>
                        @endauth
                        <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="fas fa-cog me-2 text-muted"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

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

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.getElementById('sidebar').classList.add('collapsed');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
