<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'country',
        'address',
        'email',
        'phone',
        'tax_no',
        'status',
        'is_default',
    ];

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(CompanyBankAccount::class, 'company_id', 'id');
    }
}
