@extends('layouts.admin')

@section('title', 'Analytics - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Analytics Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Analytics</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Analytics & Insights',
        'message' => 'Advanced analytics and reporting for business intelligence.',
        'progress' => 35
    ])
@endsection