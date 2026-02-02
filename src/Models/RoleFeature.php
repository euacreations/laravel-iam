<?php
namespace EuaCreations\LaravelIam\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleFeature extends Pivot
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('iam.tables.role_features');
    }
}
