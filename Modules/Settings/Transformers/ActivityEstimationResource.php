<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityEstimationResource extends JsonResource
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
            'id' => $this->resource->id,
            'from_date' => $this->resource->from_date,
            'to_date' => $this->resource->to_date,
            'opening_time' => $this->resource->opening_time,
            'closing_time' => $this->resource->closing_time,
            'adult_cost' => $this->resource->adult_cost,
            'child_cost' => $this->resource->child_cost,
        ];
    }
}
