@extends('layouts.admin')

@section('title', 'Pipeline Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Pipeline Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item">Dashboards</li>
                    <li class="breadcrumb-item active" aria-current="page">Pipeline</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
            <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard" >Main Dashboard</option>
                
                <option value="/admin/dashboards/projects">Projects Dashboard</option>
                <option value="/admin/dashboards/services">Services Overview</option>
                <option value="/admin/dashboards/pipeline" selected>Pipeline Dashboard</option>
            </select>
        </div>
    </div>
    
    <!-- Pipeline Overview Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Pipeline Value</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$3.8M</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 28% vs last quarter</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-funnel-dollar text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Opportunities</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">67</h2>
                        <small class="text-info"><i class="fas fa-plus-circle"></i> 12 new this week</small>
                    </div>
                    <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lightbulb text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Win Rate</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">42%</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 5% improvement</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trophy text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Avg. Deal Size</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$185K</h2>
                        <small class="text-warning"><i class="fas fa-minus"></i> Stable</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #f093fb, #f5576c); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-pie text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sales Pipeline Stages -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-filter me-2" style="color: var(--secondary-color);"></i>
                    Sales Pipeline Stages
                </h5>
                
                <div class="pipeline-stages">
                    <div class="row">
                        <!-- Lead Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid #6c757d; background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Lead</h6>
                                <h3 class="text-center mb-2" style="color: #6c757d;">18</h3>
                                <p class="text-center mb-3 text-muted">$650K</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Tech Plaza</strong>
                                        <div class="text-muted">$120K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Green Villa</strong>
                                        <div class="text-muted">$85K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>City Tower</strong>
                                        <div class="text-muted">$200K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Qualified Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid #17a2b8; background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Qualified</h6>
                                <h3 class="text-center mb-2" style="color: #17a2b8;">15</h3>
                                <p class="text-center mb-3 text-muted">$820K</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Marina Resort</strong>
                                        <div class="text-muted">$350K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>School Complex</strong>
                                        <div class="text-muted">$180K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Proposal Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid #ffc107; background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Proposal</h6>
                                <h3 class="text-center mb-2" style="color: #ffc107;">12</h3>
                                <p class="text-center mb-3 text-muted">$1.2M</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Hospital Wing</strong>
                                        <div class="text-muted">$450K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Mall Extension</strong>
                                        <div class="text-muted">$320K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Negotiation Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid #fd7e14; background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Negotiation</h6>
                                <h3 class="text-center mb-2" style="color: #fd7e14;">8</h3>
                                <p class="text-center mb-3 text-muted">$780K</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Office Park</strong>
                                        <div class="text-muted">$280K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Luxury Villas</strong>
                                        <div class="text-muted">$500K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contract Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid var(--secondary-color); background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Contract</h6>
                                <h3 class="text-center mb-2" style="color: var(--secondary-color);">6</h3>
                                <p class="text-center mb-3 text-muted">$420K</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Beach Resort</strong>
                                        <div class="text-muted">$220K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Corporate HQ</strong>
                                        <div class="text-muted">$200K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Won Stage -->
                        <div class="col-md-2 mb-3">
                            <div class="stage-card" style="border-top: 4px solid #28a745; background: #f8f9fa; padding: 20px; border-radius: 10px;">
                                <h6 class="text-center mb-3">Won</h6>
                                <h3 class="text-center mb-2" style="color: #28a745;">8</h3>
                                <p class="text-center mb-3 text-muted">$930K</p>
                                
                                <div class="stage-items" style="max-height: 200px; overflow-y: auto;">
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Stadium Project</strong>
                                        <div class="text-muted">$450K</div>
                                    </div>
                                    <div class="item mb-2 p-2" style="background: white; border-radius: 5px; font-size: 12px;">
                                        <strong>Hotel Renovation</strong>
                                        <div class="text-muted">$480K</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Conversion Rates -->
                <div class="conversion-rates mt-4">
                    <div class="d-flex justify-content-around align-items-center">
                        <div class="text-center">
                            <small class="text-muted">Lead → Qualified</small>
                            <h5 style="color: var(--primary-color);">83%</h5>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                        <div class="text-center">
                            <small class="text-muted">Qualified → Proposal</small>
                            <h5 style="color: var(--primary-color);">80%</h5>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                        <div class="text-center">
                            <small class="text-muted">Proposal → Negotiation</small>
                            <h5 style="color: var(--primary-color);">67%</h5>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                        <div class="text-center">
                            <small class="text-muted">Negotiation → Contract</small>
                            <h5 style="color: var(--primary-color);">75%</h5>
                        </div>
                        <i class="fas fa-arrow-right text-muted"></i>
                        <div class="text-center">
                            <small class="text-muted">Contract → Won</small>
                            <h5 style="color: var(--primary-color);">133%</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pipeline Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary-color);"></i>
                    Pipeline Velocity
                </h5>
                <div style="position: relative; height: 250px;">
                    <canvas id="velocityChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-clock me-2" style="color: var(--secondary-color);"></i>
                    Average Deal Cycle Time
                </h5>
                <div class="cycle-metrics">
                    <div class="row text-center">
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">15</h3>
                                <small class="text-white">Days in Lead</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">22</h3>
                                <small class="text-white">Days in Qualified</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, #f093fb, #f5576c); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">18</h3>
                                <small class="text-white">Days in Proposal</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">12</h3>
                                <small class="text-white">Days in Negotiation</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, #fa709a, #fee140); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">8</h3>
                                <small class="text-white">Days to Contract</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div style="background: linear-gradient(135deg, #28a745, #20c997); padding: 20px; border-radius: 10px;">
                                <h3 class="text-white">75</h3>
                                <small class="text-white">Total Days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hot Opportunities -->
    <div class="row">
        <div class="col-lg-12">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="color: var(--primary-color);">
                        <i class="fas fa-fire me-2" style="color: #ff6b6b;"></i>
                        Hot Opportunities
                    </h5>
                    <button class="btn btn-sm" style="background: var(--secondary-color); color: var(--primary-color);">
                        <i class="fas fa-plus"></i> Add Opportunity
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--secondary-color);">
                                <th>Opportunity</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Value</th>
                                <th>Stage</th>
                                <th>Probability</th>
                                <th>Close Date</th>
                                <th>Owner</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-medium">
                                    <i class="fas fa-fire text-danger me-1"></i>
                                    Marina Resort Complex
                                </td>
                                <td>Coastal Development</td>
                                <td><span class="badge" style="background: var(--secondary-color); color: var(--primary-color);">Full Service</span></td>
                                <td class="fw-bold">$850K</td>
                                <td><span class="badge bg-warning">Negotiation</span></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: 85%;">85%</div>
                                    </div>
                                </td>
                                <td>Jan 15, 2025</td>
                                <td>John Doe</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">
                                    <i class="fas fa-fire text-warning me-1"></i>
                                    Corporate Headquarters
                                </td>
                                <td>TechGiant Inc.</td>
                                <td><span class="badge" style="background: #667eea; color: white;">Engineering</span></td>
                                <td class="fw-bold">$620K</td>
                                <td><span class="badge bg-info">Proposal</span></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" style="width: 70%;">70%</div>
                                    </div>
                                </td>
                                <td>Jan 25, 2025</td>
                                <td>Sarah Miller</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">
                                    Hospital Expansion
                                </td>
                                <td>City Medical Center</td>
                                <td><span class="badge" style="background: #f093fb; color: white;">Interior Design</span></td>
                                <td class="fw-bold">$450K</td>
                                <td><span class="badge bg-primary">Qualified</span></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" style="width: 60%;">60%</div>
                                    </div>
                                </td>
                                <td>Feb 10, 2025</td>
                                <td>Mike Johnson</td>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pipeline Velocity Chart
    const velocityCtx = document.getElementById('velocityChart').getContext('2d');
    new Chart(velocityCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
            datasets: [{
                label: 'New Opportunities',
                data: [5, 8, 6, 12, 9, 15],
                borderColor: '#f3c887',
                backgroundColor: 'rgba(243, 200, 135, 0.1)',
                tension: 0.4
            }, {
                label: 'Closed Won',
                data: [2, 3, 4, 5, 3, 6],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
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
</script>
@endpush