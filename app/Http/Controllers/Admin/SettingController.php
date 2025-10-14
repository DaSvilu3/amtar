<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->paginate(20);
        return view('admin.settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|string|in:text,number,boolean,json,file',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully.');
    }

    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
            'type' => 'required|string|in:text,number,boolean,json,file',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();
        return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
    }
}
