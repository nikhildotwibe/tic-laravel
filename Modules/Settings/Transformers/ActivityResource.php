<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'activity_name' => $this->resource->activity_name,
            'destination_id' => $this->resource->destination_id,
            'destination' => $this->resource->destination,
            'sub_destination_id' => $this->resource->sub_destination_id,
            'sub_destination' => $this->resource->sub_destination,
            'contact_number' => $this->resource->contact_number,
            'contact_email' => $this->resource->contact_email,
            'description' => $this->resource->description,
            'is_active' => $this->resource->is_active,
            'activity_type_id' => $this->resource->activity_type_id,
            'activity_type' => $this->resource->activityType,
            'adult_count' => $this->resource->adult_count,
            'child_count' => $this->resource->child_count,
            'estimations' => ActivityEstimationResource::collection($this->resource->estimations)
        ];
    }
}
