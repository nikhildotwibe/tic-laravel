<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Transformers\UserResource;

class EnquiryResource extends JsonResource
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
            'agent_id' => $this->resource->agent_id,
            'destination_id' => $this->resource->destination_id,
            // 'sub_destination_id' => $this->resource->sub_destination_id,
            'sub_destinations' => SubDestinationResource::collection($this->resource->sub_destinations),
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
            'adult_count' => $this->resource->adult_count,
            'child_count' => $this->resource->child_count,
            'infant_count' => $this->resource->infant_count,
            'lead_source_id' => $this->resource->lead_source_id,
            'priority_id' => $this->resource->priority_id,
            'priority' => PriorityResource::make($this->resource->priority),
            'agent' => AgentResource::make($this->resource->agent),
            'destination' => $this->resource->destination,
            'sub_destination' => SubDestinationResource::make($this->resource->sub_destination),
            'lead_source' => LeadSourceResource::make($this->resource->lead_source),
            'requirements' => RequirementResource::collection($this->resource->requirements),

            'customer_id' => $this->resource->customer_id,
            'customer' => CustomerResource::make($this->resource->customer),
            'assigned_to' => $this->resource->assigned_to,
            'assigned_to_user' => UserResource::make($this->resource->assigned_to_user),
        ];
    }
}
