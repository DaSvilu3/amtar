@extends('layouts.admin')

@section('title', 'Clients - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Client Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Clients</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Client Management',
        'message' => 'Manage client profiles, contact information, and project history.',
        'progress' => 80
    ])
@endsection