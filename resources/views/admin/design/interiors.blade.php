@extends('layouts.admin')

@section('title', 'Interior Design')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Interior Design</h1>
        <p class="text-muted mb-0">Residential and commercial interior design services</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Interior Project
    </a>
</div>
@endsection

@section('content')
<style>
    .service-hero {
        background: linear-gradient(135deg, #6a1b9a 0%, #4a148c 100%);
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
        color: #ce93d8;
    }

    .hero-stat .label {
        font-size: 13px;
        opacity: 0.8;
    }

    .design-stages {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 30px;
    }

    .stage-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px;
        text-align: center;
    }

    .stage-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6a1b9a, #4a148c);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin: 0 auto 12px;
    }

    .stage-card h4 {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
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
        border-top: 4px solid #6a1b9a;
    }

    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

    .service-card h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .service-card h4 i {
        color: #6a1b9a;
    }

    .service-card p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .service-features {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .feature-tag {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        background: #f3e5f5;
        color: #6a1b9a;
    }

    .portfolio-section {
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

    .portfolio-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        padding: 24px;
    }

    .portfolio-item {
        position: relative;
        aspect-ratio: 4/3;
        background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .portfolio-item i {
        font-size: 32px;
        color: #9c27b0;
        opacity: 0.5;
    }

    .portfolio-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        padding: 16px;
        color: white;
    }

    .portfolio-overlay h5 {
        font-size: 13px;
        font-weight: 600;
        margin: 0;
    }

    .portfolio-overlay span {
        font-size: 11px;
        opacity: 0.8;
    }

    .recent-projects {
        margin-top: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .project-row {
        display: flex;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        gap: 20px;
    }

    .project-row:last-child {
        border-bottom: none;
    }

    .project-thumb {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .project-thumb i {
        font-size: 24px;
        color: #6a1b9a;
    }

    .project-info {
        flex: 1;
    }

    .project-info h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .project-info p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .project-meta {
        display: flex;
        gap: 24px;
        align-items: center;
    }

    .project-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .project-status.in_progress { background: #e3f2fd; color: #1565c0; }
    .project-status.completed { background: #e8f5e9; color: #2e7d32; }
    .project-status.planning { background: #f3e5f5; color: #7b1fa2; }

    .project-link {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        background: #f8fafc;
        color: #64748b;
        transition: all 0.2s;
    }

    .project-link:hover {
        background: #6a1b9a;
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
        .design-stages {
            grid-template-columns: repeat(3, 1fr);
        }

        .portfolio-grid {
            grid-template-columns: repeat(3, 1fr);
        }
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
    }

    @media (max-width: 768px) {
        .design-stages {
            grid-template-columns: repeat(2, 1fr);
        }

        .portfolio-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .services-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Hero Section -->
<div class="service-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-couch me-2"></i> Interior Design</h2>
            <p>Transforming spaces into inspiring environments through thoughtful design. From concept to completion, we create interiors that reflect your vision and enhance your lifestyle.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Interior%'))->count() }}</div>
                <div class="label">Total Projects</div>
            </div>
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Interior%'))->where('status', 'completed')->count() }}</div>
                <div class="label">Completed</div>
            </div>
        </div>
    </div>
</div>

<!-- Design Stages -->
<div class="design-stages">
    <div class="stage-card">
        <div class="stage-icon"><i class="fas fa-comments"></i></div>
        <h4>Consultation</h4>
    </div>
    <div class="stage-card">
        <div class="stage-icon"><i class="fas fa-lightbulb"></i></div>
        <h4>Concept Design</h4>
    </div>
    <div class="stage-card">
        <div class="stage-icon"><i class="fas fa-pencil-ruler"></i></div>
        <h4>Design Development</h4>
    </div>
    <div class="stage-card">
        <div class="stage-icon"><i class="fas fa-file-alt"></i></div>
        <h4>Documentation</h4>
    </div>
    <div class="stage-card">
        <div class="stage-icon"><i class="fas fa-check-circle"></i></div>
        <h4>Implementation</h4>
    </div>
</div>

<!-- Services -->
<div class="services-grid">
    <div class="service-card">
        <h4><i class="fas fa-home"></i> Residential Design</h4>
        <p>Complete interior design solutions for villas, apartments, and private residences tailored to your lifestyle.</p>
        <div class="service-features">
            <span class="feature-tag">Living Spaces</span>
            <span class="feature-tag">Bedrooms</span>
            <span class="feature-tag">Kitchens</span>
            <span class="feature-tag">Bathrooms</span>
        </div>
    </div>

    <div class="service-card">
        <h4><i class="fas fa-building"></i> Commercial Design</h4>
        <p>Professional environments for offices, retail spaces, restaurants, and hospitality projects.</p>
        <div class="service-features">
            <span class="feature-tag">Offices</span>
            <span class="feature-tag">Retail</span>
            <span class="feature-tag">Hospitality</span>
            <span class="feature-tag">Healthcare</span>
        </div>
    </div>

    <div class="service-card">
        <h4><i class="fas fa-palette"></i> Styling & Decoration</h4>
        <p>Furniture selection, art curation, and accessory styling to complete your interior vision.</p>
        <div class="service-features">
            <span class="feature-tag">Furniture</span>
            <span class="feature-tag">Lighting</span>
            <span class="feature-tag">Art</span>
            <span class="feature-tag">Accessories</span>
        </div>
    </div>

    <div class="service-card">
        <h4><i class="fas fa-cube"></i> 3D Visualization</h4>
        <p>Photorealistic renderings and virtual walkthroughs to help you visualize the final result.</p>
        <div class="service-features">
            <span class="feature-tag">3D Renders</span>
            <span class="feature-tag">Virtual Tours</span>
            <span class="feature-tag">Mood Boards</span>
        </div>
    </div>

    <div class="service-card">
        <h4><i class="fas fa-chair"></i> Custom Furniture</h4>
        <p>Bespoke furniture design and joinery solutions crafted to your exact specifications.</p>
        <div class="service-features">
            <span class="feature-tag">Built-ins</span>
            <span class="feature-tag">Joinery</span>
            <span class="feature-tag">Custom Pieces</span>
        </div>
    </div>

    <div class="service-card">
        <h4><i class="fas fa-tasks"></i> Project Management</h4>
        <p>End-to-end coordination of contractors, suppliers, and installation for seamless execution.</p>
        <div class="service-features">
            <span class="feature-tag">Coordination</span>
            <span class="feature-tag">Procurement</span>
            <span class="feature-tag">Installation</span>
        </div>
    </div>
</div>

<!-- Recent Projects -->
<div class="recent-projects">
    <div class="section-header">
        <h4><i class="fas fa-folder-open me-2"></i> Recent Interior Design Projects</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    @php
        $interiorProjects = \App\Models\Project::with(['client', 'mainService'])
            ->whereHas('mainService', fn($q) => $q->where('name', 'like', '%Interior%'))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    @endphp
    @forelse($interiorProjects as $project)
        <div class="project-row">
            <div class="project-thumb">
                <i class="fas fa-couch"></i>
            </div>
            <div class="project-info">
                <h5>{{ $project->name }}</h5>
                <p>{{ $project->client->name ?? 'N/A' }}</p>
            </div>
            <div class="project-meta">
                <span class="project-status {{ $project->status }}">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
                <a href="{{ route('admin.projects.show', $project) }}" class="project-link">View</a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-couch"></i>
            <h4>No Interior Design Projects Yet</h4>
            <p>Create a new project to get started.</p>
        </div>
    @endforelse
</div>
@endsection
