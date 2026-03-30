<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxSettingResource extends JsonResource
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
            'id'               => $this->resource->id,
            'cgst_percentage'  => $this->resource->cgst_percentage,
            'sgst_percentage'  => $this->resource->sgst_percentage,
            'igst_percentage'  => $this->resource->igst_percentage,
            'tcs_percentage'   => $this->resource->tcs_percentage,
            'created_at'       => $this->resource->created_at,
            'updated_at'       => $this->resource->updated_at,
        ];
    }
}
