<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        $skills = Skill::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
            'roles' => 'array',
            'skills' => 'array',
            'skills.*.id' => 'exists:skills,id',
            'skills.*.proficiency_level' => 'in:beginner,intermediate,advanced,expert',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        // Sync skills with proficiency levels
        if ($request->has('skills')) {
            $skillData = [];
            foreach ($request->skills as $skill) {
                if (!empty($skill['id'])) {
                    $skillData[$skill['id']] = [
                        'proficiency_level' => $skill['proficiency_level'] ?? 'intermediate',
                    ];
                }
            }
            $user->skills()->sync($skillData);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles', 'managedProjects');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $skills = Skill::orderBy('name')->get();
        $user->load('skills');
        return view('admin.users.edit', compact('user', 'roles', 'skills'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'roles' => 'array',
            'skills' => 'array',
            'skills.*.id' => 'exists:skills,id',
            'skills.*.proficiency_level' => 'in:beginner,intermediate,advanced,expert',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        // Sync skills with proficiency levels
        if ($request->has('skills')) {
            $skillData = [];
            foreach ($request->skills as $skill) {
                if (!empty($skill['id'])) {
                    $skillData[$skill['id']] = [
                        'proficiency_level' => $skill['proficiency_level'] ?? 'intermediate',
                    ];
                }
            }
            $user->skills()->sync($skillData);
        } else {
            // If no skills submitted, remove all skills
            $user->skills()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
