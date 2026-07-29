<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;

trait HasRolesAndPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('slug', $role);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->getAllPermissions()->contains('slug', $permission);
    }

    public function giveRole(Role|string $role): static
    {
        $role = is_string($role) ? Role::where('slug', $role)->firstOrFail() : $role;
        $this->roles()->syncWithoutDetaching($role);
        return $this;
    }

    public function revokeRole(Role|string $role): static
    {
        $role = is_string($role) ? Role::where('slug', $role)->firstOrFail() : $role;
        $this->roles()->detach($role);
        return $this;
    }

    public function getAllPermissions(): Collection
    {
        $this->loadMissing('roles.permissions', 'permissions');
        return $this->permissions->merge($this->roles->flatMap->permissions)->unique('id')->values();
    }
}
