@extends('layouts.admin')

@section('title', 'System Settings - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>System Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Settings</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'System Configuration',
        'message' => 'Configure system settings, preferences, and global parameters.',
        'progress' => 75
    ])
@endsection