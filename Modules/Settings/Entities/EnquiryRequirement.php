<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class EnquiryRequirement extends BaseModel
{
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];
}
