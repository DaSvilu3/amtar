@extends('layouts.admin')

@section('title', 'Fit-Out Services')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Fit-Out Services</h1>
        <p class="text-muted mb-0">Commercial and residential fit-out project management</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Fit-Out Project
    </a>
</div>
@endsection

@section('content')
<style>
    .service-hero {
        background: linear-gradient(135deg, #ef6c00 0%, #e65100 100%);
        border-radius: 16px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .service-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .hero-text h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .hero-text p {
        font-size: 15px;
        opacity: 0.9;
        max-width: 500px;
        line-height: 1.6;
    }

    .hero-stats {
        display: flex;
        gap: 40px;
    }

    .hero-stat {
        text-align: center;
    }

    .hero-stat .value {
        font-size: 36px;
        font-weight: 700;
        color: #ffcc80;
    }

    .hero-stat .label {
        font-size: 13px;
        opacity: 0.8;
    }

    .fitout-types {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .type-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 28px;
        position: relative;
        overflow: hidden;
    }

    .type-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ef6c00, #ff9800);
    }

    .type-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, #ef6c00, #e65100);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 16px;
    }

    .type-card h4 {
        font-size: 17px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 10px;
    }

    .type-card p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .type-services {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .type-services li {
        font-size: 13px;
        color: #1e293b;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .type-services li:last-child {
        border-bottom: none;
    }

    .type-services li i {
        color: #ef6c00;
        font-size: 12px;
    }

    .process-steps {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 30px;
        margin-bottom: 30px;
    }

    .process-steps h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .process-steps h3 i {
        color: #ef6c00;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
    }

    .step-item {
        text-align: center;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #fff3e0;
        color: #ef6c00;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        margin: 0 auto 10px;
    }

    .step-item h5 {
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .key-services {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .service-stat {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        text-align: center;
    }

    .service-stat i {
        font-size: 28px;
        color: #ef6c00;
        margin-bottom: 12px;
    }

    .service-stat h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .service-stat p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    .projects-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .project-table {
        width: 100%;
        border-collapse: collapse;
    }

    .project-table th {
        text-align: left;
        padding: 14px 24px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
    }

    .project-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    .project-table tr:last-child td {
        border-bottom: none;
    }

    .project-name {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .project-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: #fff3e0;
        color: #ef6c00;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .project-name-text h5 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .project-name-text span {
        font-size: 12px;
        color: #64748b;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-badge.in_progress { background: #e3f2fd; color: #1565c0; }
    .status-badge.completed { background: #e8f5e9; color: #2e7d32; }
    .status-badge.planning { background: #fff3e0; color: #ef6c00; }
    .status-badge.on_hold { background: #fce4ec; color: #c62828; }

    .progress-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress-bar {
        width: 80px;
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: #ef6c00;
        border-radius: 3px;
    }

    .action-btn {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        text-decoration: none;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #ef6c00;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }

    .empty-state i {
        font-size: 48px;
        color: #e2e8f0;
        margin-bottom: 16px;
    }

    @media (max-width: 1200px) {
        .key-services {
            grid-template-columns: repeat(2, 1fr);
        }

        .steps-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .fitout-types {
            grid-template-columns: 1fr;
        }

        .hero-content {
            flex-direction: column;
            gap: 30px;
            text-align: center;
        }
    }

    @media (max-width: 640px) {
        .key-services {
            grid-template-columns: 1fr;
        }

        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Hero Section -->
<div class="service-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-tools me-2"></i> Fit-Out Services</h2>
            <p>Comprehensive fit-out solutions from design to handover. We transform empty shells into fully functional spaces, managing every detail of the construction and installation process.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Fit%out%')->orWhere('name', 'like', '%Fitout%'))->count() }}</div>
                <div class="label">Total Projects</div>
            </div>
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Fit%out%')->orWhere('name', 'like', '%Fitout%'))->where('status', 'in_progress')->count() }}</div>
                <div class="label">Active</div>
            </div>
        </div>
    </div>
</div>

<!-- Fit-Out Types -->
<div class="fitout-types">
    <div class="type-card">
        <div class="type-icon">
            <i class="fas fa-building"></i>
        </div>
        <h4>Office Fit-Out</h4>
        <p>Creating productive workspaces tailored to your business needs and corporate culture.</p>
        <ul class="type-services">
            <li><i class="fas fa-check"></i> Open plan & private offices</li>
            <li><i class="fas fa-check"></i> Meeting rooms & boardrooms</li>
            <li><i class="fas fa-check"></i> Reception & waiting areas</li>
            <li><i class="fas fa-check"></i> Staff amenities</li>
        </ul>
    </div>

    <div class="type-card">
        <div class="type-icon">
            <i class="fas fa-store"></i>
        </div>
        <h4>Retail Fit-Out</h4>
        <p>Eye-catching retail environments that enhance customer experience and drive sales.</p>
        <ul class="type-services">
            <li><i class="fas fa-check"></i> Storefront design</li>
            <li><i class="fas fa-check"></i> Display systems</li>
            <li><i class="fas fa-check"></i> POS & checkout areas</li>
            <li><i class="fas fa-check"></i> Storage & back-of-house</li>
        </ul>
    </div>

    <div class="type-card">
        <div class="type-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <h4>F&B Fit-Out</h4>
        <p>Restaurant, cafe, and food service spaces designed for efficiency and atmosphere.</p>
        <ul class="type-services">
            <li><i class="fas fa-check"></i> Dining areas</li>
            <li><i class="fas fa-check"></i> Commercial kitchens</li>
            <li><i class="fas fa-check"></i> Bar & service counters</li>
            <li><i class="fas fa-check"></i> MEP integration</li>
        </ul>
    </div>
</div>

<!-- Process Steps -->
<div class="process-steps">
    <h3><i class="fas fa-list-ol"></i> Fit-Out Process</h3>
    <div class="steps-grid">
        <div class="step-item">
            <div class="step-number">1</div>
            <h5>Briefing</h5>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <h5>Design</h5>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <h5>Procurement</h5>
        </div>
        <div class="step-item">
            <div class="step-number">4</div>
            <h5>Construction</h5>
        </div>
        <div class="step-item">
            <div class="step-number">5</div>
            <h5>Installation</h5>
        </div>
        <div class="step-item">
            <div class="step-number">6</div>
            <h5>Handover</h5>
        </div>
    </div>
</div>

<!-- Key Services -->
<div class="key-services">
    <div class="service-stat">
        <i class="fas fa-paint-roller"></i>
        <h4>Finishes</h4>
        <p>Flooring, ceilings, walls</p>
    </div>
    <div class="service-stat">
        <i class="fas fa-door-open"></i>
        <h4>Joinery</h4>
        <p>Custom millwork & cabinetry</p>
    </div>
    <div class="service-stat">
        <i class="fas fa-plug"></i>
        <h4>MEP Works</h4>
        <p>Electrical, plumbing, HVAC</p>
    </div>
    <div class="service-stat">
        <i class="fas fa-chair"></i>
        <h4>FF&E</h4>
        <p>Furniture & equipment</p>
    </div>
</div>

<!-- Recent Projects -->
<div class="projects-section">
    <div class="section-header">
        <h4><i class="fas fa-folder-open me-2"></i> Recent Fit-Out Projects</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <table class="project-table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Type</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $fitoutProjects = \App\Models\Project::with(['client', 'mainService'])
                    ->whereHas('mainService', fn($q) => $q->where('name', 'like', '%Fit%out%')->orWhere('name', 'like', '%Fitout%'))
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp
            @forelse($fitoutProjects as $project)
                <tr>
                    <td>
                        <div class="project-name">
                            <div class="project-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="project-name-text">
                                <h5>{{ $project->name }}</h5>
                                <span>{{ $project->client->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="color: #64748b;">{{ $project->mainService->name ?? 'Fit-Out' }}</td>
                    <td>
                        <span class="status-badge {{ $project->status }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="progress-cell">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span style="font-size: 12px; color: #64748b;">{{ $project->progress }}%</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.projects.show', $project) }}" class="action-btn">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-tools"></i>
                            <h4>No Fit-Out Projects Yet</h4>
                            <p>Create a new project to get started.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
