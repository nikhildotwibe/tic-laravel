<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageTermResource extends JsonResource
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
            'id'            => $this->resource->id,
            'invoice_terms' => $this->resource->invoice_terms,
            'package_terms' => $this->resource->package_terms,
            'bank_info'     => $this->resource->bank_info,
            'created_at'    => $this->resource->created_at,
            'updated_at'    => $this->resource->updated_at,
        ];
    }
}
