@extends('layouts.admin')

@section('title', 'Messages - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Messages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Messages</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Messaging System',
        'message' => 'Internal messaging and communication platform for team collaboration.',
        'progress' => 45
    ])
@endsection