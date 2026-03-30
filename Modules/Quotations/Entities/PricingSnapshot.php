<?php

namespace Modules\Quotations\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingSnapshot extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'itinerary_id',
        'snapshot_data',
        'grand_total',
        'currency',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'snapshot_data' => 'array',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function creator()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by');
    }
}
