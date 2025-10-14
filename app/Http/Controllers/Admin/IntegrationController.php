<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = Integration::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.integrations.index', compact('integrations'));
    }

    public function create()
    {
        return view('admin.integrations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:whatsapp,email,sms,api',
            'provider' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['config'])) {
            $validated['config'] = json_encode($validated['config']);
        }

        Integration::create($validated);

        return redirect()->route('admin.integrations.index')->with('success', 'Integration created successfully.');
    }

    public function show(Integration $integration)
    {
        return view('admin.integrations.show', compact('integration'));
    }

    public function edit(Integration $integration)
    {
        return view('admin.integrations.edit', compact('integration'));
    }

    public function update(Request $request, Integration $integration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:whatsapp,email,sms,api',
            'provider' => 'nullable|string|max:255',
            'config' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['config'])) {
            $validated['config'] = json_encode($validated['config']);
        }

        $integration->update($validated);

        return redirect()->route('admin.integrations.index')->with('success', 'Integration updated successfully.');
    }

    public function destroy(Integration $integration)
    {
        $integration->delete();
        return redirect()->route('admin.integrations.index')->with('success', 'Integration deleted successfully.');
    }
}
