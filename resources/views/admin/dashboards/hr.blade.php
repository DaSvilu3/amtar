@extends('layouts.admin')

@section('title', 'HR Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title with Dashboard Selector -->
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>HR Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item">Dashboards</li>
                    <li class="breadcrumb-item active" aria-current="page">HR</li>
                </ol>
            </nav>
        </div>
        <div class="dashboard-selector">
            <select class="form-select" onchange="window.location.href=this.value" style="min-width: 200px; border: 2px solid var(--secondary-color); border-radius: 10px;">
                <option value="/admin/dashboard">Main Dashboard</option>
                <option value="/admin/dashboards/finance">Finance Dashboard</option>
                <option value="/admin/dashboards/projects">Projects Dashboard</option>
                <option value="/admin/dashboards/services">Services Overview</option>
                <option value="/admin/dashboards/pipeline">Pipeline Dashboard</option>
                <option value="/admin/dashboards/hr" selected>HR Dashboard</option>
                <option value="/admin/dashboards/performance">Performance Dashboard</option>
            </select>
        </div>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'HR Dashboard',
        'message' => 'Employee management, attendance tracking, and team analytics coming soon.',
        'progress' => 25
    ])
@endsection