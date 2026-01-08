@extends('layouts.admin')

@section('title', 'Engineering Consulting')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Engineering Consulting</h1>
        <p class="text-muted mb-0">Engineering consulting services and project coordination</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Consulting Project
    </a>
</div>
@endsection

@section('content')
<style>
    .service-hero {
        background: linear-gradient(135deg, var(--primary-color, #2f0e13) 0%, #5a2a30 100%);
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
        background: radial-gradient(circle, rgba(243, 200, 135, 0.1) 0%, transparent 70%);
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
        opacity: 0.85;
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
        color: var(--secondary-color, #f3c887);
    }

    .hero-stat .label {
        font-size: 13px;
        opacity: 0.8;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .service-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        transition: all 0.2s;
    }

    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .service-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-color, #2f0e13), #5a2a30);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 16px;
    }

    .service-card h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .service-card p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .service-card-link {
        font-size: 13px;
        font-weight: 500;
        color: var(--primary-color, #2f0e13);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .service-card-link:hover {
        text-decoration: underline;
    }

    .recent-section {
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

    .projects-table {
        width: 100%;
        border-collapse: collapse;
    }

    .projects-table th {
        text-align: left;
        padding: 14px 24px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
    }

    .projects-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }

    .projects-table tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-badge.in_progress { background: #e3f2fd; color: #1565c0; }
    .status-badge.planning { background: #f3e5f5; color: #7b1fa2; }
    .status-badge.completed { background: #e8f5e9; color: #2e7d32; }
    .status-badge.on_hold { background: #fff3e0; color: #ef6c00; }

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
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .hero-content {
            flex-direction: column;
            gap: 30px;
            text-align: center;
        }

        .hero-text p {
            max-width: 100%;
        }
    }

    @media (max-width: 640px) {
        .services-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            justify-content: center;
        }
    }
</style>

<!-- Hero Section -->
<div class="service-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-hard-hat me-2"></i> Engineering Consulting</h2>
            <p>Providing expert engineering consulting services including structural analysis, MEP design, civil engineering, and project feasibility studies for construction projects across Oman.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Engineering%'))->count() }}</div>
                <div class="label">Active Projects</div>
            </div>
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Task::whereHas('project.mainService', fn($q) => $q->where('name', 'like', '%Engineering%'))->where('status', 'completed')->count() }}</div>
                <div class="label">Tasks Completed</div>
            </div>
        </div>
    </div>
</div>

<!-- Services -->
<div class="services-grid">
    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-building"></i>
        </div>
        <h4>Structural Engineering</h4>
        <p>Comprehensive structural design and analysis for buildings, including load calculations, foundation design, and structural drawings.</p>
        <a href="{{ route('admin.projects.index') }}?service=structural" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-bolt"></i>
        </div>
        <h4>MEP Design</h4>
        <p>Mechanical, Electrical, and Plumbing systems design tailored for optimal building performance and energy efficiency.</p>
        <a href="{{ route('admin.projects.index') }}?service=mep" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-road"></i>
        </div>
        <h4>Civil Engineering</h4>
        <p>Infrastructure planning including roads, drainage systems, and site development for residential and commercial projects.</p>
        <a href="{{ route('admin.projects.index') }}?service=civil" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h4>Feasibility Studies</h4>
        <p>Technical and economic feasibility assessments to evaluate project viability and provide data-driven recommendations.</p>
        <a href="{{ route('admin.projects.index') }}?service=feasibility" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h4>Technical Documentation</h4>
        <p>Preparation of tender documents, specifications, bills of quantities, and construction documentation.</p>
        <a href="{{ route('admin.projects.index') }}?service=documentation" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="service-card">
        <div class="service-card-icon">
            <i class="fas fa-check-double"></i>
        </div>
        <h4>Code Compliance</h4>
        <p>Review and ensure all designs comply with local building codes, regulations, and international standards.</p>
        <a href="{{ route('admin.projects.index') }}?service=compliance" class="service-card-link">
            View Projects <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Recent Projects -->
<div class="recent-section">
    <div class="section-header">
        <h4><i class="fas fa-folder-open me-2"></i> Recent Engineering Consulting Projects</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    <table class="projects-table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $consultingProjects = \App\Models\Project::with(['client', 'mainService'])
                    ->whereHas('mainService', fn($q) => $q->where('name', 'like', '%Engineering%')->orWhere('name', 'like', '%Consulting%'))
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp
            @forelse($consultingProjects as $project)
                <tr>
                    <td>
                        <a href="{{ route('admin.projects.show', $project) }}" style="color: #1e293b; text-decoration: none; font-weight: 500;">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td style="color: #64748b;">{{ $project->client->name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge {{ $project->status }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 80px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                <div style="width: {{ $project->progress }}%; height: 100%; background: #4caf50; border-radius: 3px;"></div>
                            </div>
                            <span style="font-size: 12px; color: #64748b;">{{ $project->progress }}%</span>
                        </div>
                    </td>
                    <td style="color: #64748b;">{{ $project->end_date?->format('M d, Y') ?? 'TBD' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h4>No Engineering Consulting Projects Yet</h4>
                            <p>Create a new project to get started.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
