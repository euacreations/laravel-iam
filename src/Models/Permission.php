<?php

namespace EuaCreations\LaravelIam\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table;

    protected $fillable = [
        'name',
        'slug',
        'group',
        'guard_name',
        'is_builtin',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('iam.tables.permissions');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            config('iam.tables.role_permissions')
        );
    }
}
