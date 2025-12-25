Complete Plan Summary
Role-Based UX (Phases 1-4)
Navigation filtering - Each role sees only relevant menu items
Route protection - Middleware enforcement for access control
Role-specific dashboards - Engineer gets task-focused view, PM gets project view
Project Creation Enhancement (Phase 5)
New Step 5 in wizard - Task preview before creation
Show all tasks that will be generated from templates
Preview auto-assignment suggestions
Toggle individual tasks on/off
"Create Project & Generate Tasks" button
Task Assignment Improvements (Phase 6)
Priority & deadline scoring - Urgent/high priority tasks weighted higher, deadline proximity factored in
Batch assignment - Select multiple unassigned tasks, assign all with one click
Visual workload chart - Bar chart showing team utilization (green/yellow/red)
Configurable weights - Admin settings to adjust scoring factors via sliders
Files Overview
6 new files to create (services, composers, dashboard partials, workload chart)
10 files to modify (routes, controllers, views, services)
Does this comprehensive plan cover everything you need? If you're ready, I can exit plan mode and begin implementation.
Claude’s Plan
UX Enhancement Plan - AMTAR Interior Design Office Management System
Summary
Implement comprehensive role-based UX improvements for 3 active roles: Administrator, Project Manager, and Engineer. Key Issues to Fix:
Routes only use auth middleware - no role enforcement
Navigation shows everything to everyone
No role-specific dashboards (engineers see irrelevant data)
Phase 1: Navigation Filtering
Create NavigationService
File: app/Services/NavigationService.php (NEW) Centralized navigation logic that filters menu items based on user role.
Create View Composer
File: app/View/Composers/NavigationComposer.php (NEW) Bridges NavigationService to views.
Register Composer
File: app/Providers/AppServiceProvider.php Add in boot():

View::composer('layouts.admin', NavigationComposer::class);
Update Layout Navigation
File: resources/views/layouts/admin.blade.php (lines 517-701) Replace hardcoded menu with dynamic loop using $navigationSections.
Navigation by Role
Section	Admin	PM	Engineer
Dashboard	Yes	Yes	Yes
Projects	Yes	Yes	View only
Clients	Yes	Yes	No
Contracts	Yes	Yes	No
Files	Yes	Yes	Yes
Tasks	Yes	Yes	My tasks
Milestones	Yes	Yes	No
Services	Yes	No	No
Resources	Yes	No	No
Templates	Yes	No	No
System	Yes	No	No
Phase 2: Role-Based Route Protection
Restructure Routes
File: routes/web.php Group routes by access level with middleware:

// Administrator only
Route::middleware(['role:administrator'])->group(function () {
    // users, roles, settings, services, templates, skills, task-templates
});

// PM + Admin
Route::middleware(['role:administrator,project-manager'])->group(function () {
    // clients, contracts, milestones, task management
});

// Engineer + PM + Admin
Route::middleware(['role:administrator,project-manager,engineer'])->group(function () {
    // tasks (view/status), projects (view), files
});
Route Groups
Admin Only:
users.*, roles.*, settings.*
services.* (all service management)
skills.*, task-templates.*
notification-templates.*, email-templates.*, message-templates.*
document-types.*, integrations.*
PM + Admin:
clients.*, contracts.*, milestones.*
projects.* (full CRUD)
tasks.create, tasks.store, tasks.edit, tasks.update, tasks.destroy
tasks.approve, tasks.reject, tasks.suggestions, tasks.auto-assign
Engineer + PM + Admin:
tasks.index, tasks.show, tasks.update-status, tasks.submit-review
projects.index, projects.show (view only)
files.index, files.show, files.create, files.store
Phase 3: Role-Specific Dashboards
Update Dashboard Controller
File: app/Http/Controllers/Admin/DashboardController.php Modify index() to dispatch based on role:

public function index()
{
    if (auth()->user()->isAdmin()) return $this->adminDashboard();
    if (auth()->user()->isProjectManager()) return $this->projectManagerDashboard();
    return $this->engineerDashboard();
}
Create Dashboard Partials
Directory: resources/views/admin/dashboard/partials/
_admin.blade.php - System overview, user stats, all metrics
_project-manager.blade.php - My projects, team tasks, milestones
_engineer.blade.php - My tasks, workload, pending reviews
Engineer Dashboard Focus
The engineer dashboard should show:
My Assigned Tasks (priority sorted, with quick status update buttons)
Tasks Pending My Review (if reviewer)
Tasks I Submitted (awaiting approval)
My Workload (hours allocated vs capacity)
2-Week Calendar (upcoming due dates)
Visual priorities:
Urgent tasks: Red border + pulse
High priority: Orange border
Overdue: Red highlight
Phase 4: Controller-Level Authorization (Engineers)
Add Task Ownership Checks
File: app/Http/Controllers/Admin/TaskController.php In show() and updateStatus(), add:

if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
    if ($task->assigned_to !== $user->id && $task->reviewed_by !== $user->id) {
        abort(403);
    }
}
Filter Task Index for Engineers
Modify index() to filter tasks for engineers:

if ($user->hasRole('engineer') && !$user->hasAnyRole(['administrator', 'project-manager'])) {
    $tasks = $tasks->where('assigned_to', $user->id);
}
Files to Create
File	Purpose
app/Services/NavigationService.php	Centralized navigation logic
app/View/Composers/NavigationComposer.php	View composer for layout
resources/views/admin/dashboard/partials/_admin.blade.php	Admin dashboard
resources/views/admin/dashboard/partials/_project-manager.blade.php	PM dashboard
resources/views/admin/dashboard/partials/_engineer.blade.php	Engineer dashboard
Files to Modify
File	Changes
routes/web.php	Add role middleware to route groups
resources/views/layouts/admin.blade.php	Dynamic navigation rendering
app/Providers/AppServiceProvider.php	Register view composer
app/Http/Controllers/Admin/DashboardController.php	Role-specific dashboard methods
app/Http/Controllers/Admin/TaskController.php	Engineer ownership checks
app/Models/User.php	Add isEngineer() helper (optional)
Implementation Order
NavigationService + Composer - Filter sidebar immediately
Route middleware - Enforce access control
Engineer dashboard - Priority UX improvement
PM dashboard - Project-focused view
Task controller checks - Engineer task filtering
Phase 5: Project Creation - Task Auto-Generation
Current Problem
Tasks are NOT generated during project creation
User must manually navigate to generate tasks per service
No visibility into what tasks will be created
Solution: Task Preview & Confirmation Step
Add Step 5 to Project Wizard
File: resources/views/admin/projects/create-wizard.blade.php New step after Documents:
Show preview of all tasks to be generated (from templates)
Group tasks by service stage
Allow toggling individual tasks on/off
Show auto-assignment preview (who would be assigned)
"Create Project & Generate Tasks" button
Modify ProjectController::store()
File: app/Http/Controllers/Admin/ProjectController.php After project creation:

if ($request->auto_generate_tasks) {
    foreach ($project->projectServices as $projectService) {
        $this->taskAssignmentService->generateTasksFromTemplates(
            $projectService,
            null, // milestone
            $request->auto_assign_tasks ?? true
        );
    }
}
Add Task Preview API Endpoint
Route: GET /admin/api/services/{service}/task-templates-preview Returns:

{
  "templates": [
    {
      "title": "Initial Design Concept",
      "priority": "high",
      "estimated_hours": 8,
      "required_skills": ["Interior Design", "CAD"],
      "suggested_assignee": { "id": 5, "name": "John", "score": 0.85 }
    }
  ]
}
Phase 6: Enhanced Task Assignment System
6.1 Priority & Deadline-Aware Scoring
File: app/Services/TaskAssignmentService.php Update calculateAssignmentScore() to include:

// New weights (configurable via settings)
$weights = [
    'skill_match' => 0.25,      // was 0.30
    'expertise' => 0.20,        // was 0.25
    'availability' => 0.20,     // unchanged
    'workload' => 0.15,         // unchanged
    'experience' => 0.10,       // unchanged
    'urgency' => 0.10,          // NEW
];
Urgency Score Calculation:

private function calculateUrgencyScore(Task $task): float
{
    $score = 0.5; // base score

    // Priority boost
    $priorityBoost = match($task->priority) {
        'urgent' => 0.3,
        'high' => 0.2,
        'medium' => 0.1,
        'low' => 0,
    };

    // Deadline proximity boost
    if ($task->due_date) {
        $daysUntilDue = now()->diffInDays($task->due_date, false);
        if ($daysUntilDue <= 1) $score += 0.2;
        elseif ($daysUntilDue <= 3) $score += 0.15;
        elseif ($daysUntilDue <= 7) $score += 0.1;
    }

    return min(1.0, $score + $priorityBoost);
}
6.2 Batch Assignment
File: app/Http/Controllers/Admin/TaskController.php Add new method:

public function batchAutoAssign(Request $request)
{
    $taskIds = $request->validate(['task_ids' => 'required|array'])['task_ids'];
    $tasks = Task::whereIn('id', $taskIds)->whereNull('assigned_to')->get();

    $results = [];
    foreach ($tasks as $task) {
        $result = $this->taskAssignmentService->autoAssign($task);
        $results[] = [
            'task_id' => $task->id,
            'assigned_to' => $result['user']->name ?? null,
            'success' => $result['success']
        ];
    }

    return response()->json(['results' => $results]);
}
Route: POST /admin/tasks/batch-assign UI Update: Add checkbox selection to task list + "Assign Selected" button
6.3 Visual Workload View
File: resources/views/admin/tasks/partials/_workload-chart.blade.php (NEW) Create a team workload visualization component:
Horizontal bar chart showing each team member
Bar segments: Allocated hours vs Available hours
Color coding: Green (<70%), Yellow (70-90%), Red (>90%)
Clickable to filter tasks by user
Data from DashboardController:

public function getTeamWorkload()
{
    $users = User::where('is_active', true)
        ->withCount(['assignedTasks' => fn($q) => $q->whereIn('status', ['pending', 'in_progress'])])
        ->with(['capacities' => fn($q) => $q->where('week_start', now()->startOfWeek())])
        ->get()
        ->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'allocated' => $u->getCurrentWorkload(),
            'capacity' => $u->capacities->first()?->total_hours ?? 40,
            'utilization' => round(($u->getCurrentWorkload() / 40) * 100),
        ]);

    return response()->json($users);
}
6.4 Configurable Scoring Weights
Database: Add to settings table or create assignment_settings

// Default weights stored in settings
'task_assignment.weights' => [
    'skill_match' => 25,
    'expertise' => 20,
    'availability' => 20,
    'workload' => 15,
    'experience' => 10,
    'urgency' => 10,
]
File: app/Services/TaskAssignmentService.php

private function getWeights(): array
{
    $settings = Setting::where('key', 'task_assignment.weights')->first();
    $weights = $settings ? json_decode($settings->value, true) : $this->defaultWeights;

    // Normalize to ensure sum = 1.0
    $total = array_sum($weights);
    return array_map(fn($w) => $w / $total, $weights);
}
Admin UI: Add settings page section for weight adjustment with sliders
Updated Files Summary
New Files to Create
File	Purpose
app/Services/NavigationService.php	Navigation filtering
app/View/Composers/NavigationComposer.php	View composer
resources/views/admin/dashboard/partials/_admin.blade.php	Admin dashboard
resources/views/admin/dashboard/partials/_project-manager.blade.php	PM dashboard
resources/views/admin/dashboard/partials/_engineer.blade.php	Engineer dashboard
resources/views/admin/tasks/partials/_workload-chart.blade.php	Workload visualization
Files to Modify
File	Changes
routes/web.php	Add role middleware + new routes
resources/views/layouts/admin.blade.php	Dynamic navigation
app/Providers/AppServiceProvider.php	Register view composer
app/Http/Controllers/Admin/DashboardController.php	Role-specific dashboards + workload API
app/Http/Controllers/Admin/TaskController.php	Batch assign + engineer checks
app/Http/Controllers/Admin/ProjectController.php	Auto task generation
app/Services/TaskAssignmentService.php	Urgency scoring + configurable weights
resources/views/admin/projects/create-wizard.blade.php	Task preview step
resources/views/admin/tasks/index.blade.php	Batch selection + workload chart
database/seeders/SettingSeeder.php	Default assignment weights
Final Implementation Order
Phase 1-4: Role-based UX (navigation, routes, dashboards)
Phase 5: Project creation task generation with preview
Phase 6.1: Priority/deadline scoring in TaskAssignmentService
Phase 6.2: Batch assignment controller + UI
Phase 6.3: Team workload visualization
Phase 6.4: Configurable weights in settings
