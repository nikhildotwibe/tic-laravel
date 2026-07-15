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
            'is_quad_bed_available' => $this->resource->is_quad_bed_available,
            'quad_bed_amount' => $this->resource->quad_bed_amount,
            'two_bedroom_amount' => $this->resource->two_bedroom_amount,
            'three_bedroom_amount' => $this->resource->three_bedroom_amount,
            'four_bedroom_amount' => $this->resource->four_bedroom_amount,
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
            'rate_exceptions' => $this->resource->rate_exceptions
                ->groupBy(function ($exc) {
                    // Group by a fingerprint of (label + all rate amounts)
                    // so two periods with the same label but different rates are kept separate
                    return implode('|', [
                        $exc->label,
                        $exc->single_bed_amount,
                        $exc->double_bed_amount,
                        $exc->triple_bed_amount,
                        $exc->extra_bed_amount,
                        $exc->child_w_bed_amount,
                        $exc->child_n_bed_amount,
                        $exc->quad_bed_amount,
                        $exc->two_bedroom_amount,
                        $exc->three_bedroom_amount,
                        $exc->four_bedroom_amount,
                    ]);
                })
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'label'               => $first->label,
                        'dates'               => $group->pluck('exception_date')->toArray(),
                        'single_bed_amount'   => $first->single_bed_amount,
                        'double_bed_amount'   => $first->double_bed_amount,
                        'triple_bed_amount'   => $first->triple_bed_amount,
                        'extra_bed_amount'    => $first->extra_bed_amount,
                        'child_w_bed_amount'  => $first->child_w_bed_amount,
                        'child_n_bed_amount'  => $first->child_n_bed_amount,
                        'quad_bed_amount'     => $first->quad_bed_amount,
                        'two_bedroom_amount'  => $first->two_bedroom_amount,
                        'three_bedroom_amount'=> $first->three_bedroom_amount,
                        'four_bedroom_amount' => $first->four_bedroom_amount,
                    ];
                })
                ->values(),
        ];
    }
}
