<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class MealPlan extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];
}
