@extends('layouts.admin')

@section('title', 'Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Main Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
            <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard" selected>Main Dashboard</option>
                <option value="/admin/dashboards/finance">Finance Dashboard</option>
                <option value="/admin/dashboards/projects">Projects Dashboard</option>
                <option value="/admin/dashboards/services">Services Overview</option>
                <option value="/admin/dashboards/pipeline">Pipeline Dashboard</option>
                <option value="/admin/dashboards/hr">HR Dashboard</option>
                <option value="/admin/dashboards/performance">Performance Dashboard</option>
            </select>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Active Projects</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">24</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 12% from last month</small>
                    </div>
                    <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-project-diagram text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Pending Approvals</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">8</h2>
                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Requires attention</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Clients</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">156</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 5 new this week</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #f093fb, #f5576c); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Revenue (Monthly)</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$45.2K</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 18% growth</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dollar-sign text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Tasks and Recent Activities -->
    <div class="row">
        <!-- Current Tasks -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-tasks me-2" style="color: var(--secondary-color);"></i>
                        Current Tasks
                    </h5>
                    <button class="btn btn-sm" style="background: var(--secondary-color); color: var(--primary-color); border-radius: 20px; padding: 5px 20px;">
                        <i class="fas fa-plus me-1"></i> New Task
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--secondary-color);">
                                <th style="color: var(--primary-color);">Task</th>
                                <th style="color: var(--primary-color);">Project</th>
                                <th style="color: var(--primary-color);">Assigned To</th>
                                <th style="color: var(--primary-color);">Priority</th>
                                <th style="color: var(--primary-color);">Status</th>
                                <th style="color: var(--primary-color);">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" style="color: #28a745; font-size: 8px;"></i>
                                        <span class="fw-medium">Interior Design Review</span>
                                    </div>
                                </td>
                                <td>Villa Renovation</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 8px;">JD</div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: #ff6b6b; color: white;">High</span></td>
                                <td><span class="badge" style="background: #4ecdc4; color: white;">In Progress</span></td>
                                <td>Dec 15, 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" style="color: #ffc107; font-size: 8px;"></i>
                                        <span class="fw-medium">Landscape Blueprint Approval</span>
                                    </div>
                                </td>
                                <td>Garden Complex</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 8px;">SM</div>
                                        <span>Sarah Miller</span>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: #ffd93d; color: var(--primary-color);">Medium</span></td>
                                <td><span class="badge" style="background: #6c757d; color: white;">Pending</span></td>
                                <td>Dec 18, 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" style="color: #28a745; font-size: 8px;"></i>
                                        <span class="fw-medium">Engineering Supervision Report</span>
                                    </div>
                                </td>
                                <td>Office Building A</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 8px;">MJ</div>
                                        <span>Mike Johnson</span>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: #ff6b6b; color: white;">High</span></td>
                                <td><span class="badge" style="background: #ffc107; color: var(--primary-color);">Review</span></td>
                                <td>Dec 16, 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" style="color: #dc3545; font-size: 8px;"></i>
                                        <span class="fw-medium">Fitout Material Selection</span>
                                    </div>
                                </td>
                                <td>Retail Store</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 8px;">EW</div>
                                        <span>Emma Wilson</span>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: #28a745; color: white;">Low</span></td>
                                <td><span class="badge" style="background: #4ecdc4; color: white;">In Progress</span></td>
                                <td>Dec 20, 2024</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2" style="color: #28a745; font-size: 8px;"></i>
                                        <span class="fw-medium">Contract Milestone Review</span>
                                    </div>
                                </td>
                                <td>Mall Development</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 8px;">RB</div>
                                        <span>Robert Brown</span>
                                    </div>
                                </td>
                                <td><span class="badge" style="background: #ff6b6b; color: white;">High</span></td>
                                <td><span class="badge" style="background: #28a745; color: white;">Completed</span></td>
                                <td>Dec 14, 2024</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <a href="/admin/tasks" class="text-decoration-none" style="color: var(--secondary-color); font-weight: 500;">
                        View All Tasks <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-clock me-2" style="color: var(--secondary-color);"></i>
                    Recent Activities
                </h5>
                
                <div class="activity-timeline">
                    <div class="activity-item d-flex mb-3">
                        <div class="activity-icon" style="min-width: 40px; height: 40px; border-radius: 10px; background: rgba(243, 200, 135, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-file-contract" style="color: var(--secondary-color);"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 14px;">New Contract Signed</h6>
                            <p class="mb-0 text-muted" style="font-size: 12px;">Villa Renovation Project - Client: Ahmad Ali</p>
                            <small class="text-muted">2 hours ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex mb-3">
                        <div class="activity-icon" style="min-width: 40px; height: 40px; border-radius: 10px; background: rgba(76, 175, 80, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 14px;">Design Approved</h6>
                            <p class="mb-0 text-muted" style="font-size: 12px;">Interior design for Office Building A</p>
                            <small class="text-muted">5 hours ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex mb-3">
                        <div class="activity-icon" style="min-width: 40px; height: 40px; border-radius: 10px; background: rgba(255, 193, 7, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-comment" style="color: #ffc107;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 14px;">Client Feedback</h6>
                            <p class="mb-0 text-muted" style="font-size: 12px;">Requested changes in landscape design</p>
                            <small class="text-muted">1 day ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex mb-3">
                        <div class="activity-icon" style="min-width: 40px; height: 40px; border-radius: 10px; background: rgba(33, 150, 243, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-flag" style="color: #2196f3;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 14px;">Milestone Reached</h6>
                            <p class="mb-0 text-muted" style="font-size: 12px;">Phase 2 completed for Mall Development</p>
                            <small class="text-muted">2 days ago</small>
                        </div>
                    </div>
                    
                    <div class="activity-item d-flex mb-3">
                        <div class="activity-icon" style="min-width: 40px; height: 40px; border-radius: 10px; background: rgba(156, 39, 176, 0.2); display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                            <i class="fas fa-user-plus" style="color: #9c27b0;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" style="font-size: 14px;">New Team Member</h6>
                            <p class="mb-0 text-muted" style="font-size: 12px;">Lisa Anderson joined Engineering team</p>
                            <small class="text-muted">3 days ago</small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="/admin/activities" class="text-decoration-none" style="color: var(--secondary-color); font-weight: 500;">
                        View All Activities <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Project Progress Chart -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-bar me-2" style="color: var(--secondary-color);"></i>
                    Project Progress Overview
                </h5>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="progress-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">Villa Renovation</span>
                                <span style="color: var(--secondary-color);">75%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: 75%; background: linear-gradient(90deg, var(--secondary-color), #ffdb9e);" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="progress-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">Office Building A</span>
                                <span style="color: var(--secondary-color);">60%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: 60%; background: linear-gradient(90deg, #667eea, #764ba2);" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="progress-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">Garden Complex</span>
                                <span style="color: var(--secondary-color);">45%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: 45%; background: linear-gradient(90deg, #f093fb, #f5576c);" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="progress-item">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium">Mall Development</span>
                                <span style="color: var(--secondary-color);">90%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar" role="progressbar" style="width: 90%; background: linear-gradient(90deg, #4facfe, #00f2fe);" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection