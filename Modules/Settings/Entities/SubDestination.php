<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class SubDestination extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
}
