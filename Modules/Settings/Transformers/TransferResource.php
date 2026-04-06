<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'vehicle_name' => $this->resource->vehicle_name,
            'vehicle_number' => $this->resource->vehicle_number,
            'phone_number' => $this->resource->phone_number,
            'destination_id' => $this->resource->destination_id,
            'sub_destination_id' => $this->resource->sub_destination_id,
            'destination' => $this->resource->destination,
            'sub_destination' => $this->resource->sub_destination,
            'description' => $this->resource->description,
            'is_active' => $this->resource->is_active,
            'pickuppoint' => $this->resource->pickuppoint,
            'droppoint' => $this->resource->droppoint,
            'image' => $this->resource->getFirstMediaUrl('transfer-images'),
            'estimations' => TransferEstimationResource::collection($this->resource->estimations)
        ];
    }
}
