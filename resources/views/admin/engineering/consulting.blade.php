@extends('layouts.admin')

@section('title', 'Engineering Consulting - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Engineering Consulting</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">Engineering</a></li>
                <li class="breadcrumb-item active" aria-current="page">Consulting</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Engineering Consulting Module',
        'message' => 'Manage engineering consulting projects, client requirements, and technical assessments.',
        'progress' => 45
    ])
@endsection