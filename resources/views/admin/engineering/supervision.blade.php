@extends('layouts.admin')

@section('title', 'Construction Supervision')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Construction Supervision</h1>
        <p class="text-muted mb-0">Site supervision and project management services</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Supervision Project
    </a>
</div>
@endsection

@section('content')
<style>
    .service-hero {
        background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
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
        color: #bbdefb;
    }

    .hero-stat .label {
        font-size: 13px;
        opacity: 0.8;
    }

    .supervision-phases {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .phase-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        text-align: center;
        position: relative;
    }

    .phase-card::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -10px;
        transform: translateY(-50%);
        border: 6px solid transparent;
        border-left-color: #e2e8f0;
    }

    .phase-card:last-child::after {
        display: none;
    }

    .phase-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1565c0, #0d47a1);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        margin: 0 auto 12px;
    }

    .phase-card h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .phase-card p {
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    .services-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .service-block {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .service-block-header {
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .service-block-header i {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #1565c0, #0d47a1);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .service-block-header h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .service-list {
        padding: 20px 24px;
    }

    .service-list-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .service-list-item:last-child {
        border-bottom: none;
    }

    .service-list-item i {
        color: #4caf50;
        margin-top: 3px;
    }

    .service-list-item span {
        font-size: 14px;
        color: #1e293b;
    }

    .active-sites {
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

    .site-card {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .site-card:last-child {
        border-bottom: none;
    }

    .site-status {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .site-status.active { background: #4caf50; box-shadow: 0 0 8px rgba(76, 175, 80, 0.4); }
    .site-status.pending { background: #ff9800; }
    .site-status.paused { background: #9e9e9e; }

    .site-info {
        flex: 1;
    }

    .site-info h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .site-info p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .site-metrics {
        display: flex;
        gap: 24px;
    }

    .site-metric {
        text-align: center;
    }

    .site-metric .value {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .site-metric .label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
    }

    .site-actions a {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s;
    }

    .site-actions a:hover {
        background: #1565c0;
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

    @media (max-width: 1024px) {
        .supervision-phases {
            grid-template-columns: repeat(2, 1fr);
        }

        .phase-card::after {
            display: none;
        }

        .hero-content {
            flex-direction: column;
            gap: 30px;
            text-align: center;
        }

        .services-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .supervision-phases {
            grid-template-columns: 1fr;
        }

        .site-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .site-metrics {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>

<!-- Hero Section -->
<div class="service-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-hard-hat me-2"></i> Construction Supervision</h2>
            <p>Professional site supervision services ensuring quality control, safety compliance, and timely project delivery through systematic monitoring and reporting.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Supervision%'))->where('status', 'in_progress')->count() }}</div>
                <div class="label">Active Sites</div>
            </div>
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Milestone::whereHas('project.mainService', fn($q) => $q->where('name', 'like', '%Supervision%'))->where('status', 'completed')->count() }}</div>
                <div class="label">Milestones Done</div>
            </div>
        </div>
    </div>
</div>

<!-- Supervision Phases -->
<div class="supervision-phases">
    <div class="phase-card">
        <div class="phase-number">1</div>
        <h4>Pre-Construction</h4>
        <p>Review drawings, specifications, and prepare quality control plan</p>
    </div>
    <div class="phase-card">
        <div class="phase-number">2</div>
        <h4>Site Mobilization</h4>
        <p>Establish site office, safety protocols, and communication channels</p>
    </div>
    <div class="phase-card">
        <div class="phase-number">3</div>
        <h4>Active Supervision</h4>
        <p>Daily monitoring, inspections, progress tracking, and reporting</p>
    </div>
    <div class="phase-card">
        <div class="phase-number">4</div>
        <h4>Project Closeout</h4>
        <p>Final inspections, documentation, and handover procedures</p>
    </div>
</div>

<!-- Services -->
<div class="services-row">
    <div class="service-block">
        <div class="service-block-header">
            <i class="fas fa-clipboard-check"></i>
            <h4>Quality Control Services</h4>
        </div>
        <div class="service-list">
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Material inspection and approval</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Workmanship quality verification</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Testing and sampling coordination</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Non-conformance reporting and resolution</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>As-built documentation verification</span>
            </div>
        </div>
    </div>

    <div class="service-block">
        <div class="service-block-header">
            <i class="fas fa-shield-alt"></i>
            <h4>Safety & Compliance</h4>
        </div>
        <div class="service-list">
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>HSE plan implementation oversight</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Regular safety audits and inspections</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Building code compliance monitoring</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Permit and approval coordination</span>
            </div>
            <div class="service-list-item">
                <i class="fas fa-check-circle"></i>
                <span>Environmental compliance tracking</span>
            </div>
        </div>
    </div>
</div>

<!-- Active Supervision Sites -->
<div class="active-sites">
    <div class="section-header">
        <h4><i class="fas fa-map-marker-alt me-2"></i> Active Supervision Sites</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">View All Projects</a>
    </div>
    @php
        $supervisionProjects = \App\Models\Project::with(['client', 'mainService'])
            ->whereHas('mainService', fn($q) => $q->where('name', 'like', '%Supervision%'))
            ->whereIn('status', ['in_progress', 'planning'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    @endphp
    @forelse($supervisionProjects as $project)
        <div class="site-card">
            <div class="site-status {{ $project->status === 'in_progress' ? 'active' : 'pending' }}"></div>
            <div class="site-info">
                <h5>{{ $project->name }}</h5>
                <p>{{ $project->client->name ?? 'N/A' }} | {{ $project->location ?? 'Location TBD' }}</p>
            </div>
            <div class="site-metrics">
                <div class="site-metric">
                    <div class="value">{{ $project->progress }}%</div>
                    <div class="label">Progress</div>
                </div>
                <div class="site-metric">
                    <div class="value">{{ $project->tasks()->where('status', 'completed')->count() }}</div>
                    <div class="label">Tasks Done</div>
                </div>
            </div>
            <div class="site-actions">
                <a href="{{ route('admin.projects.show', $project) }}">View Details</a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-hard-hat"></i>
            <h4>No Active Supervision Sites</h4>
            <p>Supervision projects will appear here when active.</p>
        </div>
    @endforelse
</div>
@endsection
