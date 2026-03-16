<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class MealPlanEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->meal_plan_id,
            'room_id' => $this->resource->room_id,
            'meal_plan_entry_id' => $this->resource->id,
            'name' => $this->resource->meal_plan->name,
            'amount' => $this->resource->amount,
        ];
    }
}
