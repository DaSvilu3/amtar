@extends('layouts.admin')

@section('title', 'Tasks - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Tasks Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tasks</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Tasks Management',
        'message' => 'Assign, track, and manage tasks across all projects and team members.',
        'progress' => 65
    ])
@endsection