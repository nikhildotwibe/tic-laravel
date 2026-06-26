<?php

namespace Modules\Quotations\Entities;

use App\Http\Traits\UseUuidTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Settings\Entities\Activity;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\SubDestination;
use Modules\Settings\Entities\Transfer;

class ItineraryEntry extends BaseModel
{
    use HasFactory;
    use UseUuidTrait;
    use SoftDeletes;

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id', 'id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }

    public function sub_destination(): BelongsTo
    {
        return $this->belongsTo(SubDestination::class, 'sub_destination_id', 'id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'subject_id', 'id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'subject_id', 'id');
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'subject_id', 'id');
    }
}
