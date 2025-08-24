@extends('layouts.admin')

@section('title', 'Landscape Design - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Landscape Design</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">Design</a></li>
                <li class="breadcrumb-item active" aria-current="page">Landscape</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Landscape Design Module',
        'message' => 'Design outdoor spaces, gardens, and landscape architecture projects.',
        'progress' => 55
    ])
@endsection