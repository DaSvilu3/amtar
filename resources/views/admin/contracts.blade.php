@extends('layouts.admin')

@section('title', 'Contracts - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Contract Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contracts</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Contract Management',
        'message' => 'Generate, manage, and track contracts with digital signatures.',
        'progress' => 70
    ])
@endsection