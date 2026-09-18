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
            'ref_no' => $this->resource->ref_no,
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
            'package_name' => $this->resource->relationLoaded('latestItinerary')
                ? optional($this->resource->latestItinerary)->package_name
                : null,
            'status' => $this->getStatus(),
            'created_at' => $this->resource->created_at,
        ];
    }

    protected function getCnfNo()
    {
        if ($this->resource->relationLoaded('itineraries')) {
            $cnfNos = [];
            foreach ($this->resource->itineraries as $itinerary) {
                if (!empty($itinerary->tour_acknowledgement_data)) {
                    $data = $itinerary->tour_acknowledgement_data;
                    
                    // Safely decode potentially nested JSON strings
                    while (is_string($data)) {
                        $decoded = json_decode($data, true);
                        if (json_last_error() === JSON_ERROR_NONE && !is_null($decoded)) {
                            $data = $decoded;
                        } else {
                            break;
                        }
                    }
                    
                    if (is_array($data)) {
                        // 1. Root headerState cnfNo
                        if (!empty($data['headerState']['cnfNo'])) {
                            $cnfNos[] = trim($data['headerState']['cnfNo']);
                        }
                        
                        // 2. Hotel bookings ticConfirmationNo and hotelConfirmationNo
                        if (!empty($data['hotelBookings']) && is_array($data['hotelBookings'])) {
                            foreach ($data['hotelBookings'] as $booking) {
                                if (!empty($booking['ticConfirmationNo'])) {
                                    $cnfNos[] = trim($booking['ticConfirmationNo']);
                                }
                                if (!empty($booking['hotelConfirmationNo'])) {
                                    $cnfNos[] = trim($booking['hotelConfirmationNo']);
                                }
                            }
                        }
                        
                        // 3. Fallback voucherData or invoiceData cnfNo
                        if (!empty($data['voucherData']['headerState']['cnfNo'])) {
                            $cnfNos[] = trim($data['voucherData']['headerState']['cnfNo']);
                        }
                        if (!empty($data['invoiceData']['headerState']['cnfNo'])) {
                            $cnfNos[] = trim($data['invoiceData']['headerState']['cnfNo']);
                        }
                    }
                }
            }
            
            if (!empty($cnfNos)) {
                return implode(', ', array_unique($cnfNos));
            }
        }
        return null;
    }

    protected function getStatus()
    {
        if ($this->resource->relationLoaded('itineraries')) {
            $hasConfirmed = $this->resource->itineraries->contains('booking_status', 'confirmed');
            if ($hasConfirmed) {
                return 'Confirmed';
            }
            $allCancelled = $this->resource->itineraries->count() > 0 && $this->resource->itineraries->every(function ($it) {
                return $it->booking_status === 'cancelled';
            });
            if ($allCancelled) {
                return 'Cancelled';
            }
        } else {
            $hasConfirmed = $this->resource->itineraries()->where('booking_status', 'confirmed')->exists();
            if ($hasConfirmed) {
                return 'Confirmed';
            }
            $count = $this->resource->itineraries()->count();
            if ($count > 0 && $this->resource->itineraries()->where('booking_status', '!=', 'cancelled')->count() === 0) {
                return 'Cancelled';
            }
        }
        return 'Pending';
    }
}
