@extends('layouts.admin')

@section('title', 'Finance Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Finance Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item">Dashboards</li>
                    <li class="breadcrumb-item active" aria-current="page">Finance</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
            <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard">Main Dashboard</option>
                <option value="/admin/dashboards/finance" selected>Finance Dashboard</option>
                <option value="/admin/dashboards/projects">Projects Dashboard</option>
                <option value="/admin/dashboards/services">Services Overview</option>
                <option value="/admin/dashboards/pipeline">Pipeline Dashboard</option>
                <option value="/admin/dashboards/hr">HR Dashboard</option>
                <option value="/admin/dashboards/performance">Performance Dashboard</option>
            </select>
        </div>
    </div>
    
    <!-- Financial KPIs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Revenue (YTD)</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$2.4M</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 22% vs last year</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-dollar-sign text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Outstanding Invoices</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$485K</h2>
                        <small class="text-warning"><i class="fas fa-clock"></i> 32 pending</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #ffc107, #ff9800); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice-dollar text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Profit Margin</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">34.5%</h2>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 3.2% improvement</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #667eea, #764ba2); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-pie text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Cash Flow</h6>
                        <h2 class="mb-0" style="color: var(--primary-color);">$1.2M</h2>
                        <small class="text-success"><i class="fas fa-check-circle"></i> Healthy</small>
                    </div>
                    <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-money-bill-wave text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Chart and Expense Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-chart-line me-2" style="color: var(--secondary-color);"></i>
                    Revenue Trends (12 Months)
                </h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-receipt me-2" style="color: var(--secondary-color);"></i>
                    Expense Categories
                </h5>
                <div style="position: relative; height: 200px;">
                    <canvas id="expenseChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #ff6b6b; font-size: 10px;"></i> Salaries</span>
                        <strong>45%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #4ecdc4; font-size: 10px;"></i> Operations</span>
                        <strong>25%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle" style="color: #45b7d1; font-size: 10px;"></i> Marketing</span>
                        <strong>15%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-circle" style="color: #f7b731; font-size: 10px;"></i> Other</span>
                        <strong>15%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Service Revenue Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-coins me-2" style="color: var(--secondary-color);"></i>
                    Revenue by Service Category
                </h5>
                <div class="row">
                    <div class="col-md-2 text-center mb-3">
                        <div style="background: linear-gradient(135deg, var(--secondary-color), #ffdb9e); padding: 20px; border-radius: 15px;">
                            <i class="fas fa-hard-hat text-white mb-2" style="font-size: 30px;"></i>
                            <h6 class="text-white mb-1">Engineering</h6>
                            <h4 class="text-white mb-0">$780K</h4>
                            <small class="text-white">32.5% of total</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center mb-3">
                        <div style="background: linear-gradient(135deg, #667eea, #764ba2); padding: 20px; border-radius: 15px;">
                            <i class="fas fa-couch text-white mb-2" style="font-size: 30px;"></i>
                            <h6 class="text-white mb-1">Interiors</h6>
                            <h4 class="text-white mb-0">$620K</h4>
                            <small class="text-white">25.8% of total</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center mb-3">
                        <div style="background: linear-gradient(135deg, #f093fb, #f5576c); padding: 20px; border-radius: 15px;">
                            <i class="fas fa-tree text-white mb-2" style="font-size: 30px;"></i>
                            <h6 class="text-white mb-1">Landscape</h6>
                            <h4 class="text-white mb-0">$420K</h4>
                            <small class="text-white">17.5% of total</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center mb-3">
                        <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); padding: 20px; border-radius: 15px;">
                            <i class="fas fa-tools text-white mb-2" style="font-size: 30px;"></i>
                            <h6 class="text-white mb-1">Fitout</h6>
                            <h4 class="text-white mb-0">$380K</h4>
                            <small class="text-white">15.8% of total</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center mb-3">
                        <div style="background: linear-gradient(135deg, #fa709a, #fee140); padding: 20px; border-radius: 15px;">
                            <i class="fas fa-clipboard-check text-white mb-2" style="font-size: 30px;"></i>
                            <h6 class="text-white mb-1">Supervision</h6>
                            <h4 class="text-white mb-0">$200K</h4>
                            <small class="text-white">8.4% of total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions and Payment Schedule -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-exchange-alt me-2" style="color: var(--secondary-color);"></i>
                    Recent Transactions
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Dec 12</td>
                                <td>Ahmad Ali</td>
                                <td><span class="badge bg-success">Income</span></td>
                                <td class="text-success">+$45,000</td>
                                <td><i class="fas fa-check-circle text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Dec 11</td>
                                <td>Office Supplies</td>
                                <td><span class="badge bg-danger">Expense</span></td>
                                <td class="text-danger">-$2,500</td>
                                <td><i class="fas fa-check-circle text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Dec 10</td>
                                <td>Sarah Construction</td>
                                <td><span class="badge bg-success">Income</span></td>
                                <td class="text-success">+$75,000</td>
                                <td><i class="fas fa-check-circle text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Dec 9</td>
                                <td>Staff Salaries</td>
                                <td><span class="badge bg-danger">Expense</span></td>
                                <td class="text-danger">-$125,000</td>
                                <td><i class="fas fa-check-circle text-success"></i></td>
                            </tr>
                            <tr>
                                <td>Dec 8</td>
                                <td>Mall Project</td>
                                <td><span class="badge bg-success">Income</span></td>
                                <td class="text-success">+$150,000</td>
                                <td><i class="fas fa-clock text-warning"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-4" style="color: var(--primary-color);">
                    <i class="fas fa-calendar-alt me-2" style="color: var(--secondary-color);"></i>
                    Upcoming Payments
                </h5>
                <div class="payment-timeline">
                    <div class="payment-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Villa Renovation - Milestone 3</h6>
                            <small class="text-muted">Due: Dec 20, 2024</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-success">$85,000</h5>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                    </div>
                    <div class="payment-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Office Building A - Final Payment</h6>
                            <small class="text-muted">Due: Dec 25, 2024</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-success">$120,000</h5>
                            <span class="badge bg-info">Invoice Sent</span>
                        </div>
                    </div>
                    <div class="payment-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Garden Complex - Phase 2</h6>
                            <small class="text-muted">Due: Jan 5, 2025</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-success">$65,000</h5>
                            <span class="badge bg-secondary">Scheduled</span>
                        </div>
                    </div>
                    <div class="payment-item d-flex justify-content-between align-items-center mb-3 p-3" style="background: rgba(243, 200, 135, 0.1); border-radius: 10px;">
                        <div>
                            <h6 class="mb-1">Retail Store Fitout</h6>
                            <small class="text-muted">Due: Jan 10, 2025</small>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-0 text-success">$45,000</h5>
                            <span class="badge bg-secondary">Scheduled</span>
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
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [180000, 195000, 210000, 185000, 225000, 240000, 220000, 235000, 250000, 245000, 260000, 275000],
                borderColor: '#f3c887',
                backgroundColor: 'rgba(243, 200, 135, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Expenses',
                data: [120000, 125000, 130000, 115000, 135000, 140000, 130000, 145000, 150000, 140000, 155000, 160000],
                borderColor: '#2f0e13',
                backgroundColor: 'rgba(47, 14, 19, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Expense Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: ['Salaries', 'Operations', 'Marketing', 'Other'],
            datasets: [{
                data: [45, 25, 15, 15],
                backgroundColor: ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7b731']
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