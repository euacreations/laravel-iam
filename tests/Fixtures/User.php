<?php

namespace Tests\Fixtures;

use EuaCreations\LaravelIam\Models\Role;
use EuaCreations\LaravelIam\Traits\HasFeatures;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFeatures;

    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
