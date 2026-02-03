<?php

namespace EuaCreations\LaravelIam\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleFeature extends Pivot
{
    protected $table;

    /**
     * Create a new role-feature pivot instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('iam.tables.role_has_features');
    }
}
