<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->withCount('users')->latest()->get(),
            'permissionGroups' => Permission::orderBy('group')->orderBy('name')->get()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('roles.create'), 403);

        $data = $this->validated($request);
        $role = Role::create([...$data, 'slug' => Str::slug($data['name'])]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return back()->with('success', 'Role created.');
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($request->user()->hasPermission('roles.update'), 403);

        $data = $this->validated($request, $role);

        $role->update([
            'name' => $data['name'],
            'slug' => $role->slug === 'super-admin' ? 'super-admin' : Str::slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        if ($role->slug !== 'super-admin') {
            $role->permissions()->sync($data['permissions'] ?? []);
        }

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Request $request, Role $role)
    {
        abort_unless($request->user()->hasPermission('roles.delete'), 403);

        if ($role->slug === 'super-admin' || $role->users()->exists()) {
            return back()->with('error', 'Assigned or system roles cannot be deleted.');
        }

        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role)],
            'description' => ['nullable', 'string'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
    }
}
