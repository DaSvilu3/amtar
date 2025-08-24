@extends('layouts.admin')

@section('title', 'Interior Design - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Interior Design</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item"><a href="#">Design</a></li>
                <li class="breadcrumb-item active" aria-current="page">Interiors</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Interior Design Module',
        'message' => 'Create and manage interior design projects, mood boards, and client presentations.',
        'progress' => 60
    ])
@endsection