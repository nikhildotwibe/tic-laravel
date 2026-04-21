<?php

namespace Modules\User\Entities;

use App\Http\Traits\UseUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UseUuidTrait;

    protected $fillable = [];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }

    public function rolesPermissions()
    {
        return $this->hasMany(RolesPermission::class, 'role_id', 'id');
    }
}
