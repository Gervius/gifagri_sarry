<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Super-Admin');
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });
        $role->load('permissions');

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');
    }
}
