@extends('layouts.admin')

@section('title', 'Edit File')

@section('content')
<div class="fade-in">
    <div class="page-title mb-4">
        <h1>Edit File Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.files.index') }}">Files</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <div class="dashboard-card">
        <form action="{{ route('admin.files.update', $file->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">File Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $file->name) }}">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="document" {{ old('category', $file->category) == 'document' ? 'selected' : '' }}>Document</option>
                    <option value="image" {{ old('category', $file->category) == 'image' ? 'selected' : '' }}>Image</option>
                    <option value="video" {{ old('category', $file->category) == 'video' ? 'selected' : '' }}>Video</option>
                    <option value="other" {{ old('category', $file->category) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update File</button>
            </div>
        </form>
    </div>
</div>
@endsection
