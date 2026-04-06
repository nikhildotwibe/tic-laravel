<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Room extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];

    /** 
     * @return void
     * Register media collection
     */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('room-images')
            ->acceptsMimeTypes(['images/jpeg', 'image/png', 'image/jpeg']);
    }

    public function meal_plans(): HasMany
    {
        return $this->hasMany(RoomMealPlanEntry::class, 'room_id', 'id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(RoomAmenity::class, 'room_amenity_entries');
    }

    public function market_type(): BelongsTo
    {
        return $this->belongsTo(MarketType::class, 'market_type_id', 'id');
    }

    public function room_type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }
}
