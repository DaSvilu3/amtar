<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with('client', 'project', 'creator')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $projects = Project::whereIn('status', ['planning', 'in_progress'])->orderBy('name')->get();
        return view('admin.contracts.create', compact('clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:draft,pending,active,expired,cancelled',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'terms' => 'nullable|string',
            'signed_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('contracts', 'public');
        }

        $validated['created_by'] = Auth::id();

        Contract::create($validated);

        return redirect()->route('admin.contracts.index')->with('success', 'Contract created successfully.');
    }

    public function show(Contract $contract)
    {
        $contract->load('client', 'project', 'creator');
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $projects = Project::whereIn('status', ['planning', 'in_progress'])->orderBy('name')->get();
        return view('admin.contracts.edit', compact('contract', 'clients', 'projects'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,' . $contract->id,
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable|string',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:draft,pending,active,expired,cancelled',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'terms' => 'nullable|string',
            'signed_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
                Storage::disk('public')->delete($contract->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('contracts', 'public');
        }

        $contract->update($validated);

        return redirect()->route('admin.contracts.index')->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract)
    {
        // Delete associated file if exists
        if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
            Storage::disk('public')->delete($contract->file_path);
        }

        $contract->delete();
        return redirect()->route('admin.contracts.index')->with('success', 'Contract deleted successfully.');
    }
}
