@extends('layouts.admin')

@section('title', 'Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title mb-4">
        <div>
            <h1>Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Empty Dashboard -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="text-center py-5">
                    <i class="fas fa-tachometer-alt" style="font-size: 48px; color: var(--secondary-color); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--primary-color);">Welcome to Amtar Admin Dashboard</h3>
                    <p class="text-muted">Dashboard content will be available soon.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
