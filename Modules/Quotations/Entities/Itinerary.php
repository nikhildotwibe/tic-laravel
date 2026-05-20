<?php

namespace Modules\Quotations\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Settings\Entities\Destination;
use Modules\Settings\Entities\Enquiry;

class Itinerary extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;
    protected $fillable = [];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class, 'enquiry_id', 'id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ItineraryEntry::class, 'itinerary_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by', 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'updated_by', 'id');
    }

    // ── Version History Relationships ──

    /**
     * All versions that share the same parent (including self).
     */
    public function versionSiblings(): HasMany
    {
        return $this->hasMany(self::class, 'parent_itinerary_id', 'parent_itinerary_id');
    }

    /**
     * Get the next version number for this version group.
     */
    public function getNextVersionNumber(): int
    {
        return self::where('parent_itinerary_id', $this->parent_itinerary_id ?? $this->id)
                    ->max('version') + 1;
    }
}
