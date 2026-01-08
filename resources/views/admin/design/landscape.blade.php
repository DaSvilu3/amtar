@extends('layouts.admin')

@section('title', 'Landscape Design')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-1">Landscape Design</h1>
        <p class="text-muted mb-0">Outdoor spaces and landscape architecture services</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Landscape Project
    </a>
</div>
@endsection

@section('content')
<style>
    .service-hero {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
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
        color: #a5d6a7;
    }

    .hero-stat .label {
        font-size: 13px;
        opacity: 0.8;
    }

    .services-showcase {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .showcase-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: all 0.2s;
    }

    .showcase-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .showcase-image {
        height: 140px;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .showcase-image i {
        font-size: 48px;
        color: #2e7d32;
        opacity: 0.6;
    }

    .showcase-content {
        padding: 20px;
    }

    .showcase-content h4 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .showcase-content p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }

    .process-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 30px;
        margin-bottom: 30px;
    }

    .process-section h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .process-section h3 i {
        color: #2e7d32;
    }

    .process-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .process-timeline::before {
        content: '';
        position: absolute;
        top: 24px;
        left: 60px;
        right: 60px;
        height: 2px;
        background: #e2e8f0;
    }

    .process-step {
        text-align: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2e7d32, #1b5e20);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin: 0 auto 12px;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }

    .process-step h4 {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .process-step p {
        font-size: 12px;
        color: #64748b;
        margin: 0;
        max-width: 140px;
        margin: 0 auto;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }

    .feature-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 24px;
        display: flex;
        gap: 16px;
    }

    .feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #e8f5e9;
        color: #2e7d32;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .feature-content h4 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 6px;
    }

    .feature-content p {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
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

    .project-list-item {
        display: flex;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        gap: 20px;
    }

    .project-list-item:last-child {
        border-bottom: none;
    }

    .project-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-icon i {
        font-size: 20px;
        color: #2e7d32;
    }

    .project-details {
        flex: 1;
    }

    .project-details h5 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .project-details span {
        font-size: 13px;
        color: #64748b;
    }

    .project-progress {
        width: 120px;
    }

    .progress-bar-bg {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .progress-bar-fill {
        height: 100%;
        background: #4caf50;
        border-radius: 3px;
    }

    .progress-text {
        font-size: 12px;
        color: #64748b;
        text-align: right;
    }

    .project-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s;
    }

    .project-action:hover {
        background: #2e7d32;
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
        .services-showcase {
            grid-template-columns: repeat(2, 1fr);
        }

        .hero-content {
            flex-direction: column;
            gap: 30px;
            text-align: center;
        }

        .process-timeline {
            flex-wrap: wrap;
            gap: 24px;
        }

        .process-timeline::before {
            display: none;
        }

        .process-step {
            flex: 0 0 calc(50% - 12px);
        }
    }

    @media (max-width: 640px) {
        .services-showcase {
            grid-template-columns: 1fr;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .process-step {
            flex: 0 0 100%;
        }
    }
</style>

<!-- Hero Section -->
<div class="service-hero">
    <div class="hero-content">
        <div class="hero-text">
            <h2><i class="fas fa-tree me-2"></i> Landscape Design</h2>
            <p>Creating sustainable and beautiful outdoor environments that harmonize with architecture and enhance quality of life. From private gardens to commercial landscapes.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Landscape%'))->count() }}</div>
                <div class="label">Total Projects</div>
            </div>
            <div class="hero-stat">
                <div class="value">{{ \App\Models\Project::whereHas('mainService', fn($q) => $q->where('name', 'like', '%Landscape%'))->where('status', 'completed')->count() }}</div>
                <div class="label">Completed</div>
            </div>
        </div>
    </div>
</div>

<!-- Services Showcase -->
<div class="services-showcase">
    <div class="showcase-card">
        <div class="showcase-image">
            <i class="fas fa-home"></i>
        </div>
        <div class="showcase-content">
            <h4>Residential Gardens</h4>
            <p>Private villa gardens and outdoor living spaces designed for relaxation and entertainment.</p>
        </div>
    </div>

    <div class="showcase-card">
        <div class="showcase-image">
            <i class="fas fa-building"></i>
        </div>
        <div class="showcase-content">
            <h4>Commercial Landscapes</h4>
            <p>Corporate campuses, hotels, and public spaces with impactful landscape design.</p>
        </div>
    </div>

    <div class="showcase-card">
        <div class="showcase-image">
            <i class="fas fa-water"></i>
        </div>
        <div class="showcase-content">
            <h4>Water Features</h4>
            <p>Fountains, pools, and water gardens that add tranquility and visual interest.</p>
        </div>
    </div>

    <div class="showcase-card">
        <div class="showcase-image">
            <i class="fas fa-seedling"></i>
        </div>
        <div class="showcase-content">
            <h4>Native Planting</h4>
            <p>Sustainable plant selections suited to Oman's climate and water conservation goals.</p>
        </div>
    </div>
</div>

<!-- Design Process -->
<div class="process-section">
    <h3><i class="fas fa-project-diagram"></i> Our Design Process</h3>
    <div class="process-timeline">
        <div class="process-step">
            <div class="step-icon"><i class="fas fa-map-marked-alt"></i></div>
            <h4>Site Analysis</h4>
            <p>Survey existing conditions, soil, climate factors</p>
        </div>
        <div class="process-step">
            <div class="step-icon"><i class="fas fa-pencil-ruler"></i></div>
            <h4>Concept Design</h4>
            <p>Develop design concepts and master plan</p>
        </div>
        <div class="process-step">
            <div class="step-icon"><i class="fas fa-drafting-compass"></i></div>
            <h4>Detailed Design</h4>
            <p>Technical drawings, plant schedules, specifications</p>
        </div>
        <div class="process-step">
            <div class="step-icon"><i class="fas fa-hard-hat"></i></div>
            <h4>Implementation</h4>
            <p>Construction oversight and quality control</p>
        </div>
        <div class="process-step">
            <div class="step-icon"><i class="fas fa-leaf"></i></div>
            <h4>Maintenance</h4>
            <p>Ongoing care plans and landscape management</p>
        </div>
    </div>
</div>

<!-- Features -->
<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-tint"></i>
        </div>
        <div class="feature-content">
            <h4>Irrigation Design</h4>
            <p>Efficient irrigation systems with smart controllers and drip technology for water conservation.</p>
        </div>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-sun"></i>
        </div>
        <div class="feature-content">
            <h4>Outdoor Lighting</h4>
            <p>Landscape lighting design to enhance safety, ambiance, and architectural features.</p>
        </div>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-road"></i>
        </div>
        <div class="feature-content">
            <h4>Hardscape Elements</h4>
            <p>Pathways, patios, pergolas, and outdoor structures that define outdoor spaces.</p>
        </div>
    </div>

    <div class="feature-card">
        <div class="feature-icon">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="feature-content">
            <h4>Sustainable Design</h4>
            <p>Eco-friendly practices including native plants, rainwater harvesting, and permeable surfaces.</p>
        </div>
    </div>
</div>

<!-- Recent Projects -->
<div class="projects-section">
    <div class="section-header">
        <h4><i class="fas fa-folder-open me-2"></i> Recent Landscape Projects</h4>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
    </div>
    @php
        $landscapeProjects = \App\Models\Project::with(['client', 'mainService'])
            ->whereHas('mainService', fn($q) => $q->where('name', 'like', '%Landscape%'))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    @endphp
    @forelse($landscapeProjects as $project)
        <div class="project-list-item">
            <div class="project-icon">
                <i class="fas fa-tree"></i>
            </div>
            <div class="project-details">
                <h5>{{ $project->name }}</h5>
                <span>{{ $project->client->name ?? 'N/A' }}</span>
            </div>
            <div class="project-progress">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $project->progress }}%"></div>
                </div>
                <div class="progress-text">{{ $project->progress }}% complete</div>
            </div>
            <a href="{{ route('admin.projects.show', $project) }}" class="project-action">View</a>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-tree"></i>
            <h4>No Landscape Projects Yet</h4>
            <p>Create a new project to get started.</p>
        </div>
    @endforelse
</div>
@endsection
