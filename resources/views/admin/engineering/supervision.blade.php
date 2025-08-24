@extends('layouts.admin')

@section('title', 'Engineering Supervision - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Engineering Supervision</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">Engineering</a></li>
                <li class="breadcrumb-item active" aria-current="page">Supervision</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Engineering Supervision Module',
        'message' => 'Monitor construction progress, quality control, and compliance with engineering standards.',
        'progress' => 50
    ])
@endsection