<?php

namespace Modules\Settings\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;


class Room extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

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

    public function rate_exceptions(): HasMany
    {
        return $this->hasMany(RoomRateException::class, 'room_id', 'id');
    }

    /**
     * Calculate total pricing for a stay by iterating through each night and checking for rate exceptions.
     */
    public function calculateStayPricing($startDate, $endDate, $counts)
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->startOfDay();
        
        // Ensure at least 1 night if start and end are the same day.
        $nights = $start->diffInDays($end);
        if ($nights < 1) {
            $nights = 1;
        }

        // Filter loaded collection if already loaded (e.g. from eager loading)
        // Or query specifically for the date range
        $exceptions = $this->relationLoaded('rate_exceptions') 
            ? $this->rate_exceptions 
            : $this->rate_exceptions()->whereBetween('exception_date', [$start->toDateString(), $end->toDateString()])->get();

        $totals = [
            'total_amount' => 0,
            'adult_net' => 0,
            'child_w_net' => 0,
            'child_n_net' => 0,
        ];

        for ($i = 0; $i < $nights; $i++) {
            $currentDate = $start->copy()->addDays($i)->toDateString();
            
            // Find if there's an exception for this date
            $exception = $exceptions->firstWhere('exception_date', $currentDate);

            // Determine rates for this night
            $singleRate = $exception ? ($exception->single_bed_amount ?? 0) : $this->single_bed_amount;
            $doubleRate = $exception ? ($exception->double_bed_amount ?? 0) : $this->double_bed_amount;
            $tripleRate = $exception ? ($exception->triple_bed_amount ?? 0) : $this->triple_bed_amount;
            $quadRate = $exception ? ($exception->quad_bed_amount ?? 0) : $this->quad_bed_amount;
            $twoBedRate = $exception ? ($exception->two_bedroom_amount ?? 0) : $this->two_bedroom_amount;
            $threeBedRate = $exception ? ($exception->three_bedroom_amount ?? 0) : $this->three_bedroom_amount;
            $fourBedRate = $exception ? ($exception->four_bedroom_amount ?? 0) : $this->four_bedroom_amount;
            $extraRate = $exception ? ($exception->extra_bed_amount ?? 0) : $this->extra_bed_amount;
            $childWRate = $exception ? ($exception->child_w_bed_amount ?? 0) : $this->child_w_bed_amount;
            $childNRate = $exception ? ($exception->child_n_bed_amount ?? 0) : $this->child_n_bed_amount;

            // Counts
            $singleCount = $counts['single_count'] ?? 0;
            $doubleCount = $counts['double_count'] ?? 0;
            $tripleCount = $counts['triple_count'] ?? 0;
            $quadCount = $counts['quad_count'] ?? 0;
            $twoBedCount = $counts['two_bedroom_count'] ?? 0;
            $threeBedCount = $counts['three_bedroom_count'] ?? 0;
            $fourBedCount = $counts['four_bedroom_count'] ?? 0;
            $extraCount = $counts['extra_count'] ?? 0;
            $childWCount = $counts['child_w_count'] ?? 0;
            $childNCount = $counts['child_n_count'] ?? 0;

            // Night total
            $nightTotal = ($singleRate * $singleCount) +
                          ($doubleRate * $doubleCount) +
                          ($tripleRate * $tripleCount) +
                          ($quadRate * $quadCount) +
                          ($twoBedRate * $twoBedCount) +
                          ($threeBedRate * $threeBedCount) +
                          ($fourBedRate * $fourBedCount) +
                          ($extraRate * $extraCount) +
                          ($childWRate * $childWCount) +
                          ($childNRate * $childNCount);

            $totals['total_amount'] += $nightTotal;

            // Sub-totals for per-person breakdown
            $totals['adult_net'] += ($singleRate * $singleCount) + 
                                    ($doubleRate * $doubleCount) + 
                                    ($tripleRate * $tripleCount) + 
                                    ($quadRate * $quadCount) + 
                                    ($twoBedRate * $twoBedCount) + 
                                    ($threeBedRate * $threeBedCount) + 
                                    ($fourBedRate * $fourBedCount) + 
                                    ($extraRate * $extraCount);
            
            $totals['child_w_net'] += ($childWRate * $childWCount);
            $totals['child_n_net'] += ($childNRate * $childNCount);
        }

        return $totals;
    }
}
