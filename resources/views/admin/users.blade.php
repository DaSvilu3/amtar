@extends('layouts.admin')

@section('title', 'User Management - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>User Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'User Management',
        'message' => 'Manage user accounts, roles, permissions, and access control.',
        'progress' => 85
    ])
@endsection