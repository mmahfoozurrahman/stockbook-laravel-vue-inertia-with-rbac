<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('roles')->latest()->paginate(15),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('users.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', Password::min(8)],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create($data);
        $user->roles()->sync($data['roles'] ?? []);

        return back()->with('success', 'Team member created.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->hasPermission('users.update'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', Password::min(8)],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $removingSuperAdmin = $user->hasRole('super-admin')
            && ! Role::whereIn('id', $data['roles'] ?? [])
                ->where('slug', 'super-admin')
                ->exists();

        if ($removingSuperAdmin && Role::where('slug', 'super-admin')->first()?->users()->count() <= 1) {
            return back()->with('error', 'Keep at least one super administrator assigned.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        $user->roles()->sync($data['roles'] ?? []);

        return back()->with('success', 'Team member updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->hasPermission('users.delete'), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('super-admin') && Role::where('slug', 'super-admin')->first()?->users()->count() <= 1) {
            return back()->with('error', 'The last super administrator cannot be deleted.');
        }

        $user->delete();

        return back()->with('success', 'Team member removed.');
    }
}
