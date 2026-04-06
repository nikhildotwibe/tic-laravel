<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'salute' => $this->resource->salute,
            'name' => $this->resource->name,
            'mobile' => $this->resource->mobile,
            'email' => $this->resource->email,
            'description' => $this->resource->description,
        ];
    }
}
