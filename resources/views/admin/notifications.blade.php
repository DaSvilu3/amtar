@extends('layouts.admin')

@section('title', 'Notifications - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Notifications</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Notification System',
        'message' => 'Configure and manage email, WhatsApp, and system notifications.',
        'progress' => 50
    ])
@endsection