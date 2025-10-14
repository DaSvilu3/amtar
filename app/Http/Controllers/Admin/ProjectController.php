<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client', 'projectManager')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.projects.create', compact('clients', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_number' => 'required|string|max:255|unique:projects,project_number',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'status' => 'required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $project = Project::create($validated);

        if ($request->has('services')) {
            $project->services()->sync($request->services);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load('client', 'projectManager', 'services', 'contracts');
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.projects.edit', compact('project', 'clients', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_number' => 'required|string|max:255|unique:projects,project_number,' . $project->id,
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable|string',
            'status' => 'required|string|in:planning,in_progress,on_hold,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'project_manager_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $project->update($validated);

        if ($request->has('services')) {
            $project->services()->sync($request->services);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}
