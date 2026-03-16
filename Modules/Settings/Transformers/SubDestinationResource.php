<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SubDestinationResource extends JsonResource
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
            'name' => $this->resource->name,
            'destination_id' => $this->resource->destination_id,
            'destination_name' => $this->resource->destination->name,
            'deleted_at' => $this->resource->deleted_at,
        ];
    }
}
