@extends('layouts.admin')

@section('title', 'Email Templates - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Email Templates</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Email Templates</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Email Template Management',
        'message' => 'Create and manage email templates for automated communications.',
        'progress' => 55
    ])
@endsection