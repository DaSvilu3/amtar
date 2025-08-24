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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
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
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--bg-dark) 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
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
            margin-bottom: 30px;
        }
        
        .menu-section-title {
            color: var(--secondary-color);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px;
            margin-bottom: 10px;
            opacity: 0.7;
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
            padding: 12px 20px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .menu-link:hover {
            background: var(--hover-color);
            border-left-color: var(--secondary-color);
            color: var(--text-light);
        }
        
        .menu-link.active {
            background: var(--hover-color);
            border-left-color: var(--secondary-color);
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
            font-size: 18px;
            width: 30px;
            margin-right: 15px;
            color: var(--secondary-color);
        }
        
        .sidebar.collapsed .menu-icon {
            margin-right: 0;
        }
        
        .menu-text {
            font-size: 14px;
            font-weight: 500;
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
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item {
            font-size: 14px;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary-color);
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
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="/logo.jpg" alt="Amtar Logo" class="sidebar-logo">
        </div>
        
        <nav class="sidebar-menu">
            <!-- Main Menu -->
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                
                <div class="menu-item">
                    <a href="/admin/dashboard" class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </div>
            </div>
            
            <!-- Services Section -->
            <div class="menu-section">
                <div class="menu-section-title">Services</div>
                
                <div class="menu-item">
                    <a href="#" class="menu-link" onclick="toggleSubmenu(event, 'engineering-menu')">
                        <i class="fas fa-hard-hat menu-icon"></i>
                        <span class="menu-text">Engineering</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </a>
                    <div class="submenu" id="engineering-menu">
                        <a href="/admin/engineering/consulting" class="menu-link">
                            <i class="fas fa-lightbulb menu-icon"></i>
                            <span class="menu-text">Consulting</span>
                        </a>
                        <a href="/admin/engineering/supervision" class="menu-link">
                            <i class="fas fa-clipboard-check menu-icon"></i>
                            <span class="menu-text">Supervision</span>
                        </a>
                    </div>
                </div>
                
                <div class="menu-item">
                    <a href="#" class="menu-link" onclick="toggleSubmenu(event, 'design-menu')">
                        <i class="fas fa-palette menu-icon"></i>
                        <span class="menu-text">Design</span>
                        <i class="fas fa-chevron-right menu-arrow"></i>
                    </a>
                    <div class="submenu" id="design-menu">
                        <a href="/admin/design/interiors" class="menu-link">
                            <i class="fas fa-couch menu-icon"></i>
                            <span class="menu-text">Interiors</span>
                        </a>
                        <a href="/admin/design/landscape" class="menu-link">
                            <i class="fas fa-tree menu-icon"></i>
                            <span class="menu-text">Landscape</span>
                        </a>
                        <a href="/admin/design/fitout" class="menu-link">
                            <i class="fas fa-tools menu-icon"></i>
                            <span class="menu-text">Fitout</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Projects Section -->
            <div class="menu-section">
                <div class="menu-section-title">Projects</div>
                
                <div class="menu-item">
                    <a href="/admin/projects" class="menu-link">
                        <i class="fas fa-project-diagram menu-icon"></i>
                        <span class="menu-text">All Projects</span>
                        <span class="menu-badge">12</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/tasks" class="menu-link">
                        <i class="fas fa-tasks menu-icon"></i>
                        <span class="menu-text">Tasks</span>
                        <span class="menu-badge">5</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/milestones" class="menu-link">
                        <i class="fas fa-flag-checkered menu-icon"></i>
                        <span class="menu-text">Milestones</span>
                    </a>
                </div>
            </div>
            
            <!-- Clients Section -->
            <div class="menu-section">
                <div class="menu-section-title">Clients</div>
                
                <div class="menu-item">
                    <a href="/admin/clients" class="menu-link">
                        <i class="fas fa-users menu-icon"></i>
                        <span class="menu-text">Client List</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/contracts" class="menu-link">
                        <i class="fas fa-file-contract menu-icon"></i>
                        <span class="menu-text">Contracts</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/approvals" class="menu-link">
                        <i class="fas fa-check-circle menu-icon"></i>
                        <span class="menu-text">Approvals</span>
                        <span class="menu-badge">3</span>
                    </a>
                </div>
            </div>
            
            <!-- Communication Section -->
            <div class="menu-section">
                <div class="menu-section-title">Communication</div>
                
                <div class="menu-item">
                    <a href="/admin/notifications" class="menu-link">
                        <i class="fas fa-bell menu-icon"></i>
                        <span class="menu-text">Notifications</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/emails" class="menu-link">
                        <i class="fas fa-envelope menu-icon"></i>
                        <span class="menu-text">Email Templates</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/messages" class="menu-link">
                        <i class="fas fa-comments menu-icon"></i>
                        <span class="menu-text">Messages</span>
                    </a>
                </div>
            </div>
            
            <!-- Reports Section -->
            <div class="menu-section">
                <div class="menu-section-title">Reports</div>
                
                <div class="menu-item">
                    <a href="/admin/analytics" class="menu-link">
                        <i class="fas fa-chart-line menu-icon"></i>
                        <span class="menu-text">Analytics</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/reports" class="menu-link">
                        <i class="fas fa-file-alt menu-icon"></i>
                        <span class="menu-text">Reports</span>
                    </a>
                </div>
            </div>
            
            <!-- Settings Section -->
            <div class="menu-section">
                <div class="menu-section-title">Settings</div>
                
                <div class="menu-item">
                    <a href="/admin/users" class="menu-link">
                        <i class="fas fa-user-cog menu-icon"></i>
                        <span class="menu-text">User Management</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/settings" class="menu-link">
                        <i class="fas fa-cog menu-icon"></i>
                        <span class="menu-text">System Settings</span>
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="/admin/integrations" class="menu-link">
                        <i class="fas fa-plug menu-icon"></i>
                        <span class="menu-text">Integrations</span>
                    </a>
                </div>
            </div>
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
                
                <div class="user-profile dropdown">
                    <div class="user-avatar">JD</div>
                    <span class="user-name">John Doe</span>
                    <i class="fas fa-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
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