<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomRateException extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'exception_date',
        'label',
        'single_bed_amount',
        'double_bed_amount',
        'triple_bed_amount',
        'extra_bed_amount',
        'child_w_bed_amount',
        'child_n_bed_amount',
        'quad_bed_amount',
        'two_bedroom_amount',
        'three_bedroom_amount',
        'four_bedroom_amount',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
}
