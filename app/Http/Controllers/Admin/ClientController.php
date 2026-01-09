<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\File;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('relationshipManager')->orderBy('name')->paginate(20);
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        $employees = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:255',
            'secondary_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,prospect,archived',
            'relationship_manager_id' => 'nullable|exists:users,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $client = Client::create($validated);

        // Handle document uploads
        if ($request->has('documents')) {
            $this->handleDocumentUploads($request->file('documents'), $client);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['projects', 'contracts', 'files.documentType', 'relationshipManager']);
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $client->load('files.documentType');
        $documentTypes = DocumentType::where('entity_type', 'client')
            ->where('is_active', true)
            ->orderBy('is_required', 'desc')
            ->orderBy('name')
            ->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.clients.edit', compact('client', 'documentTypes', 'employees'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:255',
            'secondary_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,prospect,archived',
            'relationship_manager_id' => 'nullable|exists:users,id',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $client->update($validated);

        // Handle document uploads
        if ($request->has('documents')) {
            $this->handleDocumentUploads($request->file('documents'), $client);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }

    /**
     * Handle document uploads for a client.
     */
    private function handleDocumentUploads($documents, Client $client)
    {
        foreach ($documents as $documentSlug => $file) {
            if ($file && $file->isValid()) {
                // Find the document type
                $documentType = DocumentType::where('slug', $documentSlug)
                    ->where('entity_type', 'client')
                    ->first();

                if ($documentType) {
                    // Store the file
                    $path = $file->store('documents/clients/' . $client->id, 'public');
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();

                    // Create file record
                    File::create([
                        'name' => $documentType->name,
                        'original_name' => $originalName,
                        'file_path' => $path,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'category' => 'documents',
                        'description' => 'Client document: ' . $documentType->name,
                        'uploaded_by' => Auth::id(),
                        'is_public' => false,
                        'document_type_id' => $documentType->id,
                        'entity_type' => 'client',
                        'entity_id' => $client->id,
                    ]);
                }
            }
        }
    }
}
