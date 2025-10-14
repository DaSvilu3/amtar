<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $templates = MessageTemplate::orderBy('category')->orderBy('name')->paginate(20);
        return view('admin.message-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.message-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:message_templates,slug',
            'content' => 'required|string',
            'channel' => 'required|string|in:sms,whatsapp,push,in_app',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        MessageTemplate::create($validated);

        return redirect()->route('admin.message-templates.index')->with('success', 'Message template created successfully.');
    }

    public function show(MessageTemplate $messageTemplate)
    {
        return view('admin.message-templates.show', compact('messageTemplate'));
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('admin.message-templates.edit', compact('messageTemplate'));
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:message_templates,slug,' . $messageTemplate->id,
            'content' => 'required|string',
            'channel' => 'required|string|in:sms,whatsapp,push,in_app',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        $messageTemplate->update($validated);

        return redirect()->route('admin.message-templates.index')->with('success', 'Message template updated successfully.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();
        return redirect()->route('admin.message-templates.index')->with('success', 'Message template deleted successfully.');
    }
}
