<?php
namespace EuaCreations\LaravelIam\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_builtin' => 'boolean',
        'auto_assign_new_features' => 'boolean',
    ];
    protected $table;

    /**
     * Create a new role model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('iam.tables.roles', 'iam_roles');
    }

    /**
     * Get features assigned to this role.
     */
    public function features()
    {
        return $this->belongsToMany(
            Feature::class,
            config('iam.tables.role_has_features')
        );
    }
}
