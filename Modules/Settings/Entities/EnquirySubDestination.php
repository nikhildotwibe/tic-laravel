<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;


class EnquirySubDestination extends BaseModel
{
    use UseUuidTrait;
    protected $fillable = [];
}
