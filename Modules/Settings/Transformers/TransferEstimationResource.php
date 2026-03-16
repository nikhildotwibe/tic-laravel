<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferEstimationResource extends JsonResource
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
            'type' => $this->resource->type,
            'from_date' => $this->resource->from_date,
            'to_date' => $this->resource->to_date,
            'cost' => $this->resource->cost,
            'adult_cost' => $this->resource->adult_cost,
            'child_cost' => $this->resource->child_cost,
        ];
    }
}
