<?php

namespace Modules\User\Entities;

use App\Http\Traits\UseUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UseUuidTrait;

    protected $fillable = [];
}
