<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('category')->orderBy('name')->paginate(20);
        return view('admin.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.email-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_email' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.show', compact('emailTemplate'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug,' . $emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'from_email' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('admin.email-templates.index')->with('success', 'Email template deleted successfully.');
    }
}
