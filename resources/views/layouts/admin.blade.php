<!DOCTYPE html>
<html lang="en" data-theme="light">
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
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        [data-theme="dark"] {
            --primary-color: #f3c887;
            --primary-light: #f8ddb0;
            --secondary-color: #2f0e13;
            --secondary-light: #4a1a22;
            --bg-light: #1e293b;
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --border-color: #334155;
            --text-primary: #f1f5f9;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            min-height: 100vh;
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        [data-theme="dark"] body {
            background: var(--bg-body);
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
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        [data-theme="dark"] .dashboard-card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        [data-theme="dark"] .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        /* Dark mode header */
        [data-theme="dark"] .header {
            background: var(--bg-card);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .header-search input {
            background: var(--bg-light);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .header-search input::placeholder {
            color: var(--text-muted);
        }

        [data-theme="dark"] .header-user {
            background: var(--bg-light);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .header-user-name {
            color: var(--text-primary);
        }

        [data-theme="dark"] .btn-toggle {
            background: var(--bg-light);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        /* Dark mode tables */
        [data-theme="dark"] .table {
            color: var(--text-primary);
        }

        [data-theme="dark"] .table-light {
            background-color: var(--bg-light) !important;
            color: var(--text-primary);
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        [data-theme="dark"] .table > :not(caption) > * > * {
            background-color: transparent;
            border-color: var(--border-color);
        }

        /* Dark mode forms */
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: var(--bg-light);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background-color: var(--bg-light);
            border-color: #f3c887;
            color: var(--text-primary);
        }

        /* Dark mode alerts */
        [data-theme="dark"] .alert {
            border-color: var(--border-color);
        }

        /* Dark mode dropdowns */
        [data-theme="dark"] .dropdown-menu {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-primary);
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: var(--bg-light);
        }

        /* Dark mode badges */
        [data-theme="dark"] .badge.bg-light {
            background-color: var(--bg-light) !important;
            color: var(--text-primary) !important;
        }

        /* Dark mode text */
        [data-theme="dark"] .text-dark {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        /* Dark mode page title */
        [data-theme="dark"] .page-title h1 {
            color: var(--text-primary);
        }

        /* Theme toggle button */
        .btn-theme-toggle {
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

        .btn-theme-toggle:hover {
            background: var(--bg-light);
            color: var(--primary-color);
        }

        .btn-theme-toggle .fa-sun { display: none; }
        .btn-theme-toggle .fa-moon { display: block; }

        [data-theme="dark"] .btn-theme-toggle .fa-sun { display: block; }
        [data-theme="dark"] .btn-theme-toggle .fa-moon { display: none; }

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
                <!-- Theme Toggle -->
                <button class="btn-theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <i class="fas fa-moon"></i>
                    <i class="fas fa-sun"></i>
                </button>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn-header" title="Notifications" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="notificationDropdownBtn">
                        <i class="fas fa-bell"></i>
                        <span class="badge notification-badge" id="notificationBadge" style="display: none;">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px; max-height: 480px; overflow: hidden;">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            <div>
                                <button class="btn btn-sm btn-link text-muted p-0 me-2" onclick="markAllNotificationsRead()" title="Mark all as read">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-link text-primary p-0">View All</a>
                            </div>
                        </div>
                        <div class="notification-list" id="notificationList" style="max-height: 350px; overflow-y: auto;">
                            <div class="text-center py-4">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="border-top px-3 py-2 text-center">
                            <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none small">
                                <i class="fas fa-external-link-alt me-1"></i>View All Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="header-divider"></div>

                <div class="header-user dropdown" data-bs-toggle="dropdown">
                    <div class="header-user-avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
                    <span class="header-user-name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>

                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('admin.users.show', auth()->id()) }}"><i class="fas fa-user me-2 text-muted"></i>My Profile</a></li>
                        @endauth
                        <li><a class="dropdown-item" href="{{ route('admin.two-factor.show') }}"><i class="fas fa-shield-alt me-2 text-muted"></i>Two-Factor Auth</a></li>
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

    <!-- jQuery (required for Select2) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Apply theme immediately to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.getElementById('sidebar').classList.add('collapsed');
            }

            // Apply saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Scroll sidebar to active item
            scrollToActiveNavItem();

            // Load notifications on page load
            loadNotifications();

            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);

            // Load notifications when dropdown is opened
            const notificationBtn = document.getElementById('notificationDropdownBtn');
            if (notificationBtn) {
                notificationBtn.addEventListener('click', loadNotifications);
            }
        });

        function loadNotifications() {
            fetch('{{ route("admin.notifications.unread") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.unread_count);
                renderNotificationList(data.notifications);
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        }

        function renderNotificationList(notifications) {
            const list = document.getElementById('notificationList');
            if (!list) return;

            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No new notifications</p>
                    </div>
                `;
                return;
            }

            list.innerHTML = notifications.map(n => `
                <a href="${n.url}" class="notification-item d-flex align-items-start p-3 text-decoration-none border-bottom" data-id="${n.id}">
                    <div class="notification-icon bg-${n.color} bg-opacity-10 text-${n.color} me-3">
                        <i class="fas ${n.icon}"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong class="text-dark d-block text-truncate">${n.title}</strong>
                            <small class="text-muted ms-2 flex-shrink-0">${n.created_at}</small>
                        </div>
                        <p class="text-muted small mb-0 text-truncate">${n.message}</p>
                    </div>
                </a>
            `).join('');
        }

        function markAllNotificationsRead() {
            fetch('{{ route("admin.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(0);
                    loadNotifications();
                }
            });
        }

        function scrollToActiveNavItem() {
            const sidebarNav = document.querySelector('.sidebar-nav');
            const activeItem = document.querySelector('.nav-link.active');

            if (sidebarNav && activeItem) {
                // Get the position of the active item relative to the sidebar nav
                const activeRect = activeItem.getBoundingClientRect();
                const navRect = sidebarNav.getBoundingClientRect();

                // Calculate the scroll position to center the active item
                const scrollTop = activeItem.offsetTop - (sidebarNav.clientHeight / 2) + (activeItem.clientHeight / 2);

                // Smooth scroll to the active item
                sidebarNav.scrollTo({
                    top: Math.max(0, scrollTop),
                    behavior: 'smooth'
                });
            }
        }
    </script>

    <style>
        /* Notification Dropdown Styles */
        .notification-dropdown {
            padding: 0;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 12px;
        }

        .notification-item {
            transition: background-color 0.15s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .notification-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }
    </style>

    @stack('scripts')
</body>
</html>
