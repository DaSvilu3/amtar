@extends('layouts.admin')

@section('title', 'Approvals - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Approval System</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Approvals</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Approval Management',
        'message' => 'Manage client approvals, digital signatures, and approval workflows.',
        'progress' => 60
    ])
@endsection