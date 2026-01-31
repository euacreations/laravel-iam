<?php
namespace EuaCreations\LaravelIam\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];


    protected $casts = [
        'is_builtin' => 'boolean',
        'auto_assign_new_permissions' => 'boolean',
    ];
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('iam.tables.roles', 'iam_roles');
    }    
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            config('iam.tables.role_has_permissions')
        );
    }
}
