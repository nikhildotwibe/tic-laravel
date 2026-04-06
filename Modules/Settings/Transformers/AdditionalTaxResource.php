<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AdditionalTaxResource extends JsonResource
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
            'id'         => $this->resource->id,
            'name'       => $this->resource->name,
            'percentage' => $this->resource->percentage,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
