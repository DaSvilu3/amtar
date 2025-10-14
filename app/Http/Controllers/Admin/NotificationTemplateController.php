<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::orderBy('name')->paginate(20);
        return view('admin.notification-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.notification-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:notification_templates,slug',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:email,sms,push,whatsapp',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        NotificationTemplate::create($validated);

        return redirect()->route('admin.notification-templates.index')->with('success', 'Notification template created successfully.');
    }

    public function show(NotificationTemplate $notificationTemplate)
    {
        return view('admin.notification-templates.show', compact('notificationTemplate'));
    }

    public function edit(NotificationTemplate $notificationTemplate)
    {
        return view('admin.notification-templates.edit', compact('notificationTemplate'));
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:notification_templates,slug,' . $notificationTemplate->id,
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string|in:email,sms,push,whatsapp',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['variables'])) {
            $validated['variables'] = json_encode($validated['variables']);
        }

        $notificationTemplate->update($validated);

        return redirect()->route('admin.notification-templates.index')->with('success', 'Notification template updated successfully.');
    }

    public function destroy(NotificationTemplate $notificationTemplate)
    {
        $notificationTemplate->delete();
        return redirect()->route('admin.notification-templates.index')->with('success', 'Notification template deleted successfully.');
    }
}
