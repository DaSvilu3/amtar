@extends('layouts.admin')

@section('title', 'Milestones - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Project Milestones</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Milestones</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Milestones Tracking',
        'message' => 'Track project milestones, deliverables, and payment schedules.',
        'progress' => 75
    ])
@endsection