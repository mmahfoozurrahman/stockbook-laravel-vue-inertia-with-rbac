<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Lookup/Index', [
            'type' => 'permissions',
            'fields' => [
                ['name' => 'name', 'label' => 'Permission name', 'type' => 'text'],
                ['name' => 'slug', 'label' => 'Permission slug', 'type' => 'text'],
                ['name' => 'group', 'label' => 'Group', 'type' => 'text'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'editor'],
            ],
            'items' => Permission::withCount('roles')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);
        Permission::create($this->validated($request));

        return back()->with('success', 'Permission created.');
    }

    public function update(Request $request, Permission $permission)
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);
        $permission->update($this->validated($request, $permission));

        return back()->with('success', 'Permission updated.');
    }

    public function destroy(Request $request, Permission $permission)
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);
        $permission->delete();

        return back()->with('success', 'Permission deleted.');
    }

    private function validated(Request $request, ?Permission $permission = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission)],
            'group' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
