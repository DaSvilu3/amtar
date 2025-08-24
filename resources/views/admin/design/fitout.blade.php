@extends('layouts.admin')

@section('title', 'Fitout Design - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Fitout Design</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">Design</a></li>
                <li class="breadcrumb-item active" aria-current="page">Fitout</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Fitout Design Module',
        'message' => 'Manage commercial and residential fitout projects from concept to completion.',
        'progress' => 40
    ])
@endsection