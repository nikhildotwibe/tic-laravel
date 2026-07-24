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

    protected $casts = [
        'is_current' => 'boolean',
        'booking_status_updated_at' => 'datetime',
        'tour_acknowledgement_data' => 'array',
    ];

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
        return $this->hasMany(ItineraryEntry::class, 'itinerary_id', 'id')->orderBy('sort_order', 'asc');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by', 'id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'updated_by', 'id');
    }

    public function currency_obj(): BelongsTo
    {
        return $this->belongsTo(\Modules\Settings\Entities\Currency::class, 'currency', 'id');
    }

    // ── Versioning ──────────────────────────────────────────────

    /**
     * Get all versions that share the same parent (including self).
     */
    public function allVersions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_itinerary_id', 'parent_itinerary_id');
    }

    /**
     * Clone the current state into a new "history" row before an update.
     * The original record keeps its ID and stays is_current=true with an incremented version.
     *
     * @return self|null  The newly created history clone, or null if nothing to snapshot.
     */
    public function createVersionSnapshot(): ?self
    {
        // Reload entries to ensure we have the latest state
        $this->load('entries');

        // Don't snapshot if there are no entries yet (first-time setup)
        if ($this->entries->isEmpty()) {
            return null;
        }

        // 1. Clone the itinerary record as a history row
        $clone = $this->replicate(['id', 'seq']);
        $clone->parent_itinerary_id = $this->parent_itinerary_id ?? $this->id;
        $clone->version    = $this->version;
        $clone->is_current = false;
        
        // Disable timestamps to preserve original edit time on the historical version
        $clone->timestamps = false;
        $clone->created_at = $this->created_at;
        $clone->updated_at = $this->updated_at;
        $clone->save();

        // 2. Clone all entries, linking them to the new history itinerary
        foreach ($this->entries as $entry) {
            $entryClone = $entry->replicate(['id', 'seq']);
            $entryClone->itinerary_id = $clone->id;
            $entryClone->timestamps = false;
            $entryClone->created_at = $entry->created_at;
            $entryClone->updated_at = $entry->updated_at;
            $entryClone->save();
        }

        // 3. Increment version on the original and ensure parent is set
        $this->version = ($this->version ?? 1) + 1;
        if (!$this->parent_itinerary_id) {
            $this->parent_itinerary_id = $this->id;
        }
        $this->is_current = true;
        $this->save();

        return $clone;
    }
}
