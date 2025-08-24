@extends('layouts.admin')

@section('title', 'Projects - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>All Projects</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Projects</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Projects Management',
        'message' => 'View and manage all active and completed projects across all service categories.',
        'progress' => 70
    ])
@endsection