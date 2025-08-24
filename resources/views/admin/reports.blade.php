@extends('layouts.admin')

@section('title', 'Reports - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Reports</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Reports Generation',
        'message' => 'Generate comprehensive reports for projects, finances, and performance.',
        'progress' => 40
    ])
@endsection