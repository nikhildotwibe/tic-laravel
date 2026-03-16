<?php

namespace Modules\User\Entities;

use App\Http\Traits\UseUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UseUuidTrait;

    protected $fillable = [];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'id');
    }

    public function read_permissions()
    {
        return $this->permissions()->where('slug', 'LIKE', '%read%');
    }

    public function write_permissions()
    {
        return $this->permissions()->where('slug', 'LIKE', '%write%');
    }

    public function update_permissions()
    {
        return $this->permissions()->where('slug', 'LIKE', '%update%');
    }

    public function delete_permissions()
    {
        return $this->permissions()->where('slug', 'LIKE', '%delete%');
    }
}
