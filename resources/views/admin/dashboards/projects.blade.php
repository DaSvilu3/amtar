@extends('layouts.admin')

@section('title', 'Projects Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Projects Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item">Dashboards</li>
                    <li class="breadcrumb-item active" aria-current="page">Projects</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
            <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard" >Main Dashboard</option>
                
                <option value="/admin/dashboards/projects" selected>Projects Dashboard</option>
                <option value="/admin/dashboards/services" >Services Overview</option>
                <option value="/admin/dashboards/pipeline" >Pipeline Dashboard</option>
            </select>
        </div>
    </div>
    
    <!-- Project Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Active Projects</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">42</h2>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" style="width: 75%; background: var(--secondary-color);"></div>
                        </div>
                        <small class="text-muted">75% capacity</small>
                    </div>
                    <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-briefcase text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">On Schedule</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">28</h2>
                        <small class="text-success"><i class="fas fa-check-circle"></i> 66.7% on track</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">At Risk</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">8</h2>
                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Need attention</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #ffc107, #ff9800); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Completed (Month)</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">6</h2>
                        <small class="text-info"><i class="fas fa-trophy"></i> $450K delivered</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-flag-checkered text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Project Timeline & Gantt -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-stream me-2" style="color: var(--secondary-color);"></i>
                        Project Timeline
                    </h5>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-secondary active">Week</button>
                        <button class="btn btn-sm btn-outline-secondary">Month</button>
                        <button class="btn btn-sm btn-outline-secondary">Quarter</button>
                    </div>
                </div>
                
                <!-- Gantt Chart Style Timeline -->
                <div class="project-timeline">
                    @php
                        $projects = [
                            ['name' => 'Villa Renovation', 'start' => 10, 'duration' => 60, 'progress' => 75, 'color' => '#f3c887'],
                            ['name' => 'Office Building A', 'start' => 5, 'duration' => 80, 'progress' => 60, 'color' => '#667eea'],
                            ['name' => 'Garden Complex', 'start' => 25, 'duration' => 50, 'progress' => 45, 'color' => '#f093fb'],
                            ['name' => 'Mall Development', 'start' => 0, 'duration' => 90, 'progress' => 90, 'color' => '#4facfe'],
                            ['name' => 'Retail Store Fitout', 'start' => 40, 'duration' => 40, 'progress' => 30, 'color' => '#fa709a'],
                            ['name' => 'Residential Complex', 'start' => 20, 'duration' => 70, 'progress' => 55, 'color' => '#28a745'],
                        ];
                    @endphp
                    
                    @foreach($projects as $project)
                    <div class="timeline-item mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <strong>{{ $project['name'] }}</strong>
                            </div>
                            <div class="col-md-10">
                                <div class="timeline-bar" style="position: relative; height: 30px; background: #f0f0f0; border-radius: 5px;">
                                    <div style="position: absolute; left: {{ $project['start'] }}%; width: {{ $project['duration'] }}%; height: 100%; background: {{ $project['color'] }}20; border-radius: 5px;">
                                        <div style="width: {{ $project['progress'] }}%; height: 100%; background: {{ $project['color'] }}; border-radius: 5px; position: relative;">
                                            <span style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); color: white; font-size: 12px; font-weight: 600;">{{ $project['progress'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="timeline-legend mt-3 d-flex justify-content-center gap-4">
                    <span><i class="fas fa-square" style="color: #f0f0f0;"></i> Planned</span>
                    <span><i class="fas fa-square" style="color: var(--secondary-color);"></i> Completed</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Project Details Table -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-list-alt me-2" style="color: var(--secondary-color);"></i>
                        Active Projects Details
                    </h5>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" placeholder="Search projects..." style="width: 200px;">
                        <button class="btn btn-sm" style="background: var(--secondary-color); color: var(--primary-color);">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--secondary-color);">
                                <th>Project</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Team Lead</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Budget</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-medium">Villa Renovation</td>
                                <td>Ahmad Ali</td>
                                <td><span class="badge" style="background: var(--secondary-color); color: var(--primary-color);">Interior Design</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 25px; height: 25px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; margin-right: 5px;">JD</div>
                                        <span>John Doe</span>
                                    </div>
                                </td>
                                <td>Oct 15, 2024</td>
                                <td>Jan 15, 2025</td>
                                <td>$250,000</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: 75%; background: linear-gradient(90deg, var(--secondary-color), #ffdb9e);">75%</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">On Track</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Office Building A</td>
                                <td>TechCorp Inc.</td>
                                <td><span class="badge" style="background: #667eea; color: white;">Engineering</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 25px; height: 25px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; margin-right: 5px;">SM</div>
                                        <span>Sarah Miller</span>
                                    </div>
                                </td>
                                <td>Sep 1, 2024</td>
                                <td>Feb 28, 2025</td>
                                <td>$450,000</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: 60%; background: linear-gradient(90deg, #667eea, #764ba2);">60%</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-success">On Track</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Garden Complex</td>
                                <td>Green Living Ltd.</td>
                                <td><span class="badge" style="background: #f093fb; color: white;">Landscape</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 25px; height: 25px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; margin-right: 5px;">MJ</div>
                                        <span>Mike Johnson</span>
                                    </div>
                                </td>
                                <td>Nov 1, 2024</td>
                                <td>Mar 31, 2025</td>
                                <td>$180,000</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: 45%; background: linear-gradient(90deg, #f093fb, #f5576c);">45%</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning">At Risk</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Mall Development</td>
                                <td>City Properties</td>
                                <td><span class="badge" style="background: #4facfe; color: white;">Full Service</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 25px; height: 25px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; margin-right: 5px;">RB</div>
                                        <span>Robert Brown</span>
                                    </div>
                                </td>
                                <td>Jun 1, 2024</td>
                                <td>Dec 31, 2024</td>
                                <td>$1,200,000</td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: 90%; background: linear-gradient(90deg, #4facfe, #00f2fe);">90%</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-info">Closing</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Allocation & Resource Management -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-users-cog me-2" style="color: var(--secondary-color);"></i>
                    Team Allocation
                </h5>
                <div class="team-allocation">
                    <div class="team-member mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 10px;">JD</div>
                                <div>
                                    <strong>John Doe</strong>
                                    <small class="text-muted d-block">Senior Architect</small>
                                </div>
                            </div>
                            <span class="badge bg-danger">95% Allocated</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: 95%; background: #ff6b6b;"></div>
                        </div>
                    </div>
                    
                    <div class="team-member mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 10px;">SM</div>
                                <div>
                                    <strong>Sarah Miller</strong>
                                    <small class="text-muted d-block">Project Manager</small>
                                </div>
                            </div>
                            <span class="badge bg-warning">75% Allocated</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: 75%; background: #ffc107;"></div>
                        </div>
                    </div>
                    
                    <div class="team-member mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 10px;">MJ</div>
                                <div>
                                    <strong>Mike Johnson</strong>
                                    <small class="text-muted d-block">Landscape Designer</small>
                                </div>
                            </div>
                            <span class="badge bg-success">60% Allocated</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: 60%; background: #28a745;"></div>
                        </div>
                    </div>
                    
                    <div class="team-member mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--secondary-color); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; margin-right: 10px;">EW</div>
                                <div>
                                    <strong>Emma Wilson</strong>
                                    <small class="text-muted d-block">Interior Designer</small>
                                </div>
                            </div>
                            <span class="badge bg-info">40% Allocated</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: 40%; background: #17a2b8;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-area me-2" style="color: var(--secondary-color);"></i>
                    Project Health Metrics
                </h5>
                <div style="position: relative; height: 250px;">
                    <canvas id="projectHealthChart"></canvas>
                </div>
                <div class="row mt-3">
                    <div class="col-4 text-center">
                        <h3 style="color: #28a745;">28</h3>
                        <small class="text-muted">Healthy</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 style="color: #ffc107;">8</h3>
                        <small class="text-muted">At Risk</small>
                    </div>
                    <div class="col-4 text-center">
                        <h3 style="color: #dc3545;">6</h3>
                        <small class="text-muted">Critical</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Project Health Chart
    const healthCtx = document.getElementById('projectHealthChart').getContext('2d');
    new Chart(healthCtx, {
        type: 'radar',
        data: {
            labels: ['Schedule', 'Budget', 'Quality', 'Resources', 'Client Satisfaction', 'Risk'],
            datasets: [{
                label: 'Current Status',
                data: [85, 70, 90, 65, 95, 60],
                borderColor: '#f3c887',
                backgroundColor: 'rgba(243, 200, 135, 0.2)',
                pointBackgroundColor: '#f3c887',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#f3c887'
            }, {
                label: 'Target',
                data: [90, 85, 95, 80, 100, 50],
                borderColor: '#2f0e13',
                backgroundColor: 'rgba(47, 14, 19, 0.1)',
                pointBackgroundColor: '#2f0e13',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#2f0e13'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endpush