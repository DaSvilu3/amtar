<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }

        Role::create($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('users');
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['permissions'])) {
            $validated['permissions'] = json_encode($validated['permissions']);
        }

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Display the permissions matrix view.
     */
    public function matrix()
    {
        $roles = Role::where('is_active', true)->withCount('users')->get();

        // Define all available permissions grouped by module
        $permissionGroups = [
            'Dashboard' => [
                'dashboard.view' => 'View Dashboard',
                'dashboard.analytics' => 'View Analytics',
                'dashboard.reports' => 'View Reports',
            ],
            'Projects' => [
                'projects.view' => 'View Projects',
                'projects.create' => 'Create Projects',
                'projects.edit' => 'Edit Projects',
                'projects.delete' => 'Delete Projects',
                'projects.manage-team' => 'Manage Project Team',
            ],
            'Tasks' => [
                'tasks.view' => 'View Tasks',
                'tasks.view-all' => 'View All Tasks',
                'tasks.create' => 'Create Tasks',
                'tasks.edit' => 'Edit Tasks',
                'tasks.delete' => 'Delete Tasks',
                'tasks.assign' => 'Assign Tasks',
                'tasks.approve' => 'Approve Tasks',
                'tasks.reject' => 'Reject Tasks',
            ],
            'Clients' => [
                'clients.view' => 'View Clients',
                'clients.create' => 'Create Clients',
                'clients.edit' => 'Edit Clients',
                'clients.delete' => 'Delete Clients',
            ],
            'Contracts' => [
                'contracts.view' => 'View Contracts',
                'contracts.create' => 'Create Contracts',
                'contracts.edit' => 'Edit Contracts',
                'contracts.delete' => 'Delete Contracts',
                'contracts.print' => 'Print Contracts',
            ],
            'Milestones' => [
                'milestones.view' => 'View Milestones',
                'milestones.create' => 'Create Milestones',
                'milestones.edit' => 'Edit Milestones',
                'milestones.delete' => 'Delete Milestones',
            ],
            'Files' => [
                'files.view' => 'View Files',
                'files.upload' => 'Upload Files',
                'files.edit' => 'Edit Files',
                'files.delete' => 'Delete Files',
            ],
            'Users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
            ],
            'Roles' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
            ],
            'Services' => [
                'services.view' => 'View Services',
                'services.manage' => 'Manage Services',
            ],
            'Settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
            ],
        ];

        return view('admin.roles.matrix', compact('roles', 'permissionGroups'));
    }

    /**
     * Update permissions via AJAX.
     */
    public function updatePermission(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|string',
            'enabled' => 'required|boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $permissions = $role->permissions ?? [];

        if ($validated['enabled']) {
            if (!in_array($validated['permission'], $permissions)) {
                $permissions[] = $validated['permission'];
            }
        } else {
            $permissions = array_values(array_filter($permissions, fn($p) => $p !== $validated['permission']));
        }

        $role->permissions = $permissions;
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'permissions' => $role->permissions,
        ]);
    }
}
