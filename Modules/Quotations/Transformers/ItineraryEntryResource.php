<?php

namespace Modules\Quotations\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Settings\Entities\Activity;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\Transfer;
use Modules\Settings\Transformers\ActivityResource;
use Modules\Settings\Transformers\EnquiryResource;
use Modules\Settings\Transformers\HotelResource;
use Modules\Settings\Transformers\RoomResource;
use Modules\Settings\Transformers\TransferResource;

class ItineraryEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        [$subjectModel, $subjectResource] = match ($this->resource->entry_type) {
            'HOTEL' => [Hotel::class, HotelResource::class],
            'ACTIVITY' => [Activity::class, ActivityResource::class],
            'TRANSFER' => [Transfer::class, TransferResource::class],
        };

        return [
            'id' => $this->resource->id,
            'seq' => $this->resource->sort_order,
            'date' => $this->resource->date,
            'itinerary_id' => $this->resource->itinerary_id,
            'entry_type' => $this->resource->entry_type,
            'subject_id' => $this->resource->subject_id,
            'subject' => $subjectResource::make($subjectModel::find($this->resource->subject_id)),
            'option' => $this->resource->option,
            'room_id' => $this->resource->room_id,
            'room' => RoomResource::make($this->resource->room),
            'no_of_person' => $this->resource->no_of_person,
            'single_count' => $this->resource->single_count,
            'double_count' => $this->resource->double_count,
            'triple_count' => $this->resource->triple_count,
            'quad_count' => $this->resource->quad_count,
            'two_bedroom_count' => $this->resource->two_bedroom_count,
            'three_bedroom_count' => $this->resource->three_bedroom_count,
            'four_bedroom_count' => $this->resource->four_bedroom_count,
            'extra_count' => $this->resource->extra_count,
            'child_w_count' => $this->resource->child_w_count,
            'child_n_count' => $this->resource->child_n_count,
            'room_rows' => $this->resource->room_rows ? json_decode($this->resource->room_rows, true) : null,
            'description' => $this->resource->description,
            'transfer_type' => $this->resource->transfer_type,
            'vehicle_type' => $this->resource->vehicle_type,
            'vehicle_count' => $this->resource->vehicle_count,
            'cost' => $this->resource->cost,
            'adult_cost' => $this->resource->adult_cost,
            'child_cost' => $this->resource->child_cost,
            'adult_count' => $this->resource->adult_count,
            'child_count' => $this->resource->child_count,
            'start_date' => $this->resource->start_date,
            'start_time' => $this->resource->start_time,
            'end_date' => $this->resource->end_date,
            'end_time' => $this->resource->end_time,
            'amount' => $this->resource->amount,
            'markup' => $this->resource->markup,
            'sub_destination_id' => $this->resource->destination_id,
            'sub_destination' => $this->resource->sub_destination,
        ];
    }
}
