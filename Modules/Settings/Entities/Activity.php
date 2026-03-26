<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activity extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    public function sub_destination(): BelongsTo
    {
        return $this->belongsTo(SubDestination::class, 'sub_destination_id', 'id');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id', 'id');
    }

    public function estimations(): HasMany
    {
        return $this->hasMany(ActivityEstimation::class, 'activity_id', 'id');
    }
}
