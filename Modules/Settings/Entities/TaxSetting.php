<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxSetting extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $table = 'tax_settings';

    protected $fillable = [
        'cgst_percentage',
        'sgst_percentage',
        'igst_percentage',
        'tcs_percentage',
    ];

    protected $casts = [
        'cgst_percentage' => 'float',
        'sgst_percentage' => 'float',
        'igst_percentage' => 'float',
        'tcs_percentage'  => 'float',
    ];
}
