<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::orderBy('entity_type')
            ->orderBy('name')
            ->get()
            ->groupBy('entity_type');

        return view('admin.document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.document-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:document_types,slug',
            'entity_type' => 'required|string|in:client,project,contract',
            'is_required' => 'boolean',
            'description' => 'nullable|string',
            'file_types' => 'nullable|array',
            'file_types.*' => 'string',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided or empty
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default values for checkboxes
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        DocumentType::create($validated);

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType)
    {
        return view('admin.document-types.show', compact('documentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_types', 'slug')->ignore($documentType->id),
            ],
            'entity_type' => 'required|string|in:client,project,contract',
            'is_required' => 'boolean',
            'description' => 'nullable|string',
            'file_types' => 'nullable|array',
            'file_types.*' => 'string',
            'is_active' => 'boolean',
        ]);

        // Set default values for checkboxes
        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        $documentType->update($validated);

        return redirect()
            ->route('admin.document-types.index')
            ->with('success', 'Document type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        try {
            $documentType->delete();
            return redirect()
                ->route('admin.document-types.index')
                ->with('success', 'Document type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.document-types.index')
                ->with('error', 'Failed to delete document type. It may be in use.');
        }
    }
}
