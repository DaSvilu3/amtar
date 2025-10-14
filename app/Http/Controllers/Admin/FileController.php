<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = File::with('uploadedBy')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.files.index', compact('files'));
    }

    public function create()
    {
        return view('admin.files.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'name' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();

        $filePath = $uploadedFile->store('files', 'public');

        $file = File::create([
            'name' => $validated['name'] ?? pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'uploaded_by' => Auth::id(),
            'is_public' => $validated['is_public'] ?? false,
        ]);

        return redirect()->route('admin.files.index')->with('success', 'File uploaded successfully.');
    }

    public function show(File $file)
    {
        $file->load('uploadedBy');
        return view('admin.files.show', compact('file'));
    }

    public function edit(File $file)
    {
        return view('admin.files.edit', compact('file'));
    }

    public function update(Request $request, File $file)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $file->update($validated);

        return redirect()->route('admin.files.index')->with('success', 'File updated successfully.');
    }

    public function destroy(File $file)
    {
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return redirect()->route('admin.files.index')->with('success', 'File deleted successfully.');
    }
}
