<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        // dd($this->resource->media);
        return [
            'id' => $this->resource->id,
            'market_type_id' => $this->resource->market_type_id,
            'market_type_name' => optional($this->resource->market_type)->name,
            'from_date' => $this->resource->from_date,
            'to_date' => $this->resource->to_date,
            'room_type_id' => $this->resource->room_type_id,
            'room_type_name' => optional($this->resource->room_type)->name,
            'single_bed_amount' => $this->resource->single_bed_amount,
            'double_bed_amount' => $this->resource->double_bed_amount,
            'is_triple_bed_available' => $this->resource->is_triple_bed_available,
            'triple_bed_amount' => $this->resource->triple_bed_amount,
            'is_extra_bed_available' => $this->resource->is_extra_bed_available,
            'extra_bed_amount' => $this->resource->extra_bed_amount,
            'is_child_w_bed_available' => $this->resource->is_child_w_bed_available,
            'child_w_bed_amount' => $this->resource->child_w_bed_amount,
            'is_child_n_bed_available' => $this->resource->is_child_n_bed_available,
            'child_n_bed_amount' => $this->resource->child_n_bed_amount,
            'occupancy' => $this->resource->occupancy,
            'is_allotted' => $this->resource->is_allotted,
            'allotted_cut_off_days' => $this->resource->allotted_cut_off_days,
            'meal_plans' => MealPlanEntryResource::collection($this->resource->meal_plans),
            'amenities' => AmenityResource::collection($this->resource->amenities),
            'media' => MediaResource::collection($this->resource->media),
        ];
    }
}
