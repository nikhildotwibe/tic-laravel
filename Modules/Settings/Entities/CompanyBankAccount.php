<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBankAccount extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $table = 'company_bank_accounts';

    protected $fillable = [
        'company_id',
        'bank_name',
        'account_name',
        'account_number',
        'swift_code',
        'branch_name',
        'is_primary',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
