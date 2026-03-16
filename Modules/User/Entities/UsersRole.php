<?php

namespace Modules\User\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersRole extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    use UseUuidTrait;

    protected $fillable = ['id', 'user_id', 'role_id'];
}
