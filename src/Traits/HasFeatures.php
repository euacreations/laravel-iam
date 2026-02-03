<?php

namespace EuaCreations\LaravelIam\Traits;

use Illuminate\Support\Collection;

trait HasFeatures
{
    /**
     * Check whether the user has a specific role (by slug).
     */
    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    /**
     * Check whether the user has a feature (by slug).
     */
    public function hasFeature(string $feature): bool
    {
        if (! $this->role) {
            return false;
        }

        return $this->role
            ->features
            ->contains('slug', $feature);
    }

    /**
     * Get all features for this user.
     */
    public function features(): Collection
    {
        if (! $this->role) {
            return collect();
        }

        return $this->role->features;
    }
}
