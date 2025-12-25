@extends('layouts.admin')

@section('title', 'Dashboard - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title mb-4">
        <div>
            <h1>
                @if($dashboardType === 'admin')
                    System Dashboard
                @elseif($dashboardType === 'project-manager')
                    My Projects Dashboard
                @else
                    My Tasks Dashboard
                @endif
            </h1>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    @if($dashboardType === 'admin')
        @include('admin.dashboard.partials._admin')
    @elseif($dashboardType === 'project-manager')
        @include('admin.dashboard.partials._project-manager')
    @else
        @include('admin.dashboard.partials._engineer')
    @endif
@endsection
