@extends('layouts.admin')

@section('title', 'Services Overview - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Services Overview</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item">Dashboards</li>
                    <li class="breadcrumb-item active" aria-current="page">Services</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
           <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard" >Main Dashboard</option>
                
                <option value="/admin/dashboards/projects">Projects Dashboard</option>
                <option value="/admin/dashboards/services" selected>Services Overview</option>
                <option value="/admin/dashboards/pipeline" >Pipeline Dashboard</option>
            </select>
        </div>
    </div>
    
    <!-- Service Categories Performance -->
    <div class="row mb-4">
        <!-- Engineering Consulting -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card" style="border-top: 4px solid #667eea;">
                <div class="service-header mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 style="color: var(--primary-color);">
                            <i class="fas fa-hard-hat me-2" style="color: #667eea;"></i>
                            Engineering Consulting
                        </h5>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                
                <div class="service-metrics">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Projects</small>
                            <h4 style="color: #667eea;">12</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Revenue (Month)</small>
                            <h4 style="color: #667eea;">$125K</h4>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Utilization Rate</small>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar" style="width: 85%; background: #667eea;">85%</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Customer Satisfaction</small>
                        <div class="d-flex gap-1 mt-1">
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star-half-alt" style="color: #ffc107;"></i>
                            <span class="ms-2">4.5</span>
                        </div>
                    </div>
                    
                    <div class="service-team">
                        <small class="text-muted">Team Members</small>
                        <div class="d-flex mt-2">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">JD</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">SM</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">RB</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white;">+5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Interior Design -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card" style="border-top: 4px solid #f093fb;">
                <div class="service-header mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 style="color: var(--primary-color);">
                            <i class="fas fa-couch me-2" style="color: #f093fb;"></i>
                            Interior Design
                        </h5>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                
                <div class="service-metrics">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Projects</small>
                            <h4 style="color: #f093fb;">8</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Revenue (Month)</small>
                            <h4 style="color: #f093fb;">$95K</h4>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Utilization Rate</small>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar" style="width: 92%; background: #f093fb;">92%</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Customer Satisfaction</small>
                        <div class="d-flex gap-1 mt-1">
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <span class="ms-2">4.8</span>
                        </div>
                    </div>
                    
                    <div class="service-team">
                        <small class="text-muted">Team Members</small>
                        <div class="d-flex mt-2">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #f093fb; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">EW</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #f093fb; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">LA</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #f093fb; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">TK</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #f093fb; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white;">+3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Landscape Design -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card" style="border-top: 4px solid #28a745;">
                <div class="service-header mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 style="color: var(--primary-color);">
                            <i class="fas fa-tree me-2" style="color: #28a745;"></i>
                            Landscape Design
                        </h5>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
                
                <div class="service-metrics">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Projects</small>
                            <h4 style="color: #28a745;">6</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Revenue (Month)</small>
                            <h4 style="color: #28a745;">$65K</h4>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Utilization Rate</small>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar" style="width: 70%; background: #28a745;">70%</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Customer Satisfaction</small>
                        <div class="d-flex gap-1 mt-1">
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star" style="color: #ffc107;"></i>
                            <i class="fas fa-star-half-alt" style="color: #ffc107;"></i>
                            <span class="ms-2">4.6</span>
                        </div>
                    </div>
                    
                    <div class="service-team">
                        <small class="text-muted">Team Members</small>
                        <div class="d-flex mt-2">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">MJ</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">KL</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: -10px; border: 2px solid white;">PR</div>
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white;">+2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Performance Charts -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-bar me-2" style="color: var(--secondary-color);"></i>
                    Service Performance Comparison
                </h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="serviceComparisonChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-percentage me-2" style="color: var(--secondary-color);"></i>
                    Service Mix
                </h5>
                <div style="position: relative; height: 200px;">
                    <canvas id="serviceMixChart"></canvas>
                </div>
                <div class="service-legend mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #667eea; font-size: 10px;"></i> Engineering</span>
                        <strong>32%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #f093fb; font-size: 10px;"></i> Interiors</span>
                        <strong>26%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #28a745; font-size: 10px;"></i> Landscape</span>
                        <strong>18%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #4facfe; font-size: 10px;"></i> Fitout</span>
                        <strong>16%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-circle" style="color: #ffc107; font-size: 10px;"></i> Supervision</span>
                        <strong>8%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Requests & Active Tasks -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-inbox me-2" style="color: var(--secondary-color);"></i>
                        Recent Service Requests
                    </h5>
                    <span class="badge bg-danger">12 New</span>
                </div>
                
                <div class="service-requests">
                    <div class="request-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Villa Interior Redesign</h6>
                            <small class="text-muted">Client: Mohammed Ahmed | Service: Interior Design</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Review</button>
                    </div>
                    
                    <div class="request-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Office Building Structure Analysis</h6>
                            <small class="text-muted">Client: TechStart Inc. | Service: Engineering</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Review</button>
                    </div>
                    
                    <div class="request-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Garden Design for Resort</h6>
                            <small class="text-muted">Client: Paradise Hotels | Service: Landscape</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Review</button>
                    </div>
                    
                    <div class="request-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Restaurant Fitout Project</h6>
                            <small class="text-muted">Client: Gourmet Group | Service: Fitout</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Review</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-trophy me-2" style="color: var(--secondary-color);"></i>
                    Top Performing Services (This Month)
                </h5>
                
                <div class="performance-list">
                    <div class="performance-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning me-2" style="font-size: 16px;">1</span>
                                <strong>Interior Design</strong>
                            </div>
                            <span class="text-success">+15%</span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Projects</small>
                                <strong>8</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Revenue</small>
                                <strong>$95K</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Rating</small>
                                <strong>4.8★</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="performance-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2" style="font-size: 16px;">2</span>
                                <strong>Engineering Consulting</strong>
                            </div>
                            <span class="text-success">+12%</span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Projects</small>
                                <strong>12</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Revenue</small>
                                <strong>$125K</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Rating</small>
                                <strong>4.5★</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="performance-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info me-2" style="font-size: 16px;">3</span>
                                <strong>Landscape Design</strong>
                            </div>
                            <span class="text-success">+8%</span>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Projects</small>
                                <strong>6</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Revenue</small>
                                <strong>$65K</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Rating</small>
                                <strong>4.6★</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Service Comparison Chart
    const comparisonCtx = document.getElementById('serviceComparisonChart').getContext('2d');
    new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: ['Engineering', 'Interiors', 'Landscape', 'Fitout', 'Supervision'],
            datasets: [{
                label: 'Projects',
                data: [12, 8, 6, 5, 4],
                backgroundColor: 'rgba(243, 200, 135, 0.8)'
            }, {
                label: 'Revenue ($K)',
                data: [125, 95, 65, 55, 35],
                backgroundColor: 'rgba(47, 14, 19, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Service Mix Chart
    const mixCtx = document.getElementById('serviceMixChart').getContext('2d');
    new Chart(mixCtx, {
        type: 'pie',
        data: {
            labels: ['Engineering', 'Interiors', 'Landscape', 'Fitout', 'Supervision'],
            datasets: [{
                data: [32, 26, 18, 16, 8],
                backgroundColor: ['#667eea', '#f093fb', '#28a745', '#4facfe', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush