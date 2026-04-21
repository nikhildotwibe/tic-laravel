<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
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
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'address' => $this->resource->address,
            'country_id' => $this->resource->country_id,
            'country_name' => $this->resource->country->name,
        ];
    }
}
