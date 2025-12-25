<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskTemplate;
use App\Models\Service;
use App\Models\ServiceStage;
use App\Models\Skill;
use Illuminate\Http\Request;

class TaskTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskTemplate::with(['service', 'serviceStage']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('service_stage_id')) {
            $query->where('service_stage_id', $request->service_stage_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $templates = $query->orderBy('service_id')
            ->orderBy('sort_order')
            ->paginate(20);

        $services = Service::orderBy('name')->get();
        $stages = ServiceStage::orderBy('sort_order')->get();

        return view('admin.task-templates.index', compact('templates', 'services', 'stages'));
    }

    public function create()
    {
        $services = Service::with('serviceStage')->orderBy('name')->get();
        $stages = ServiceStage::orderBy('sort_order')->get();
        $skills = Skill::where('is_active', true)->orderBy('name')->get();
        $templates = TaskTemplate::where('is_active', true)->orderBy('title')->get();

        return view('admin.task-templates.create', compact('services', 'stages', 'skills', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_stage_id' => 'nullable|exists:service_stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|integer|min:1',
            'default_duration_days' => 'nullable|integer|min:1',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'exists:skills,id',
            'required_expertise_level' => 'nullable|in:junior,mid,senior,lead',
            'requires_review' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:task_templates,id',
        ]);

        $validated['requires_review'] = $request->boolean('requires_review');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['required_skills'] = $request->input('required_skills', []);

        $template = TaskTemplate::create($validated);

        if ($request->filled('dependencies')) {
            $template->dependencies()->attach($request->dependencies);
        }

        return redirect()->route('admin.task-templates.index')
            ->with('success', 'Task template created successfully.');
    }

    public function show(TaskTemplate $taskTemplate)
    {
        $taskTemplate->load(['service', 'serviceStage', 'dependencies', 'dependents']);
        $requiredSkills = $taskTemplate->getRequiredSkillModels();

        return view('admin.task-templates.show', compact('taskTemplate', 'requiredSkills'));
    }

    public function edit(TaskTemplate $taskTemplate)
    {
        $taskTemplate->load('dependencies');
        $services = Service::with('serviceStage')->orderBy('name')->get();
        $stages = ServiceStage::orderBy('sort_order')->get();
        $skills = Skill::where('is_active', true)->orderBy('name')->get();
        $templates = TaskTemplate::where('is_active', true)
            ->where('id', '!=', $taskTemplate->id)
            ->orderBy('title')
            ->get();

        return view('admin.task-templates.edit', compact('taskTemplate', 'services', 'stages', 'skills', 'templates'));
    }

    public function update(Request $request, TaskTemplate $taskTemplate)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_stage_id' => 'nullable|exists:service_stages,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|integer|min:1',
            'default_duration_days' => 'nullable|integer|min:1',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'exists:skills,id',
            'required_expertise_level' => 'nullable|in:junior,mid,senior,lead',
            'requires_review' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:task_templates,id',
        ]);

        $validated['requires_review'] = $request->boolean('requires_review');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['required_skills'] = $request->input('required_skills', []);

        $taskTemplate->update($validated);
        $taskTemplate->dependencies()->sync($request->input('dependencies', []));

        return redirect()->route('admin.task-templates.index')
            ->with('success', 'Task template updated successfully.');
    }

    public function destroy(TaskTemplate $taskTemplate)
    {
        $taskTemplate->delete();

        return redirect()->route('admin.task-templates.index')
            ->with('success', 'Task template deleted successfully.');
    }
}
