<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageTerm extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $table = 'package_terms';

    protected $fillable = [
        'invoice_terms',
        'package_terms',
        'bank_info',
    ];
}
