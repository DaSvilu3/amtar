@extends('layouts.admin')

@section('title', 'Integrations - Amtar Admin')

@section('content')
    <!-- Page Title -->
    <div class="page-title">
        <h1>Integrations</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Integrations</li>
            </ol>
        </nav>
    </div>
    
    @include('components.coming-soon', [
        'title' => 'Third-Party Integrations',
        'message' => 'Connect with WhatsApp, Dropbox, and other external services.',
        'progress' => 30
    ])
@endsection