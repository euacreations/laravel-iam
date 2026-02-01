<?php

namespace EuaCreations\LaravelIam\Traits;

use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Check if user has a specific role (by slug)
     */
    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    /**
     * Check if user has a permission (by slug)
     */
    public function hasPermission(string $permission): bool
    {
        if (! $this->role) {
            return false;
        }

        return $this->role
            ->permissions
            ->contains('slug', $permission);
    }

    /**
     * Laravel-compatible ability check
     */
    public function can($ability, $arguments = []): bool
    {
        return $this->hasPermission($ability);
    }

    /**
     * Get all permission slugs for this user
     */
    public function permissions(): Collection
    {
        if (! $this->role) {
            return collect();
        }

        return $this->role->permissions;
    }
}
