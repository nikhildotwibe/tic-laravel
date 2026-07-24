<?php

namespace Modules\Quotations\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Settings\Transformers\EnquiryResource;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\ActivityEstimation;

class ItineraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $netAmount = 0;

        $netItemsAmount = 0;

        foreach ($this->resource->entries as $entry) {
            $netItemsAmount += $entry->amount * (1 + ($entry->markup * 0.01));
        }

        $netAmount += $netItemsAmount;

        if ($this->resource->extra_markup_percentage > 0) {
            $netAmount += $netAmount *  $this->resource->extra_markup_percentage * 0.01;
        } elseif ($this->resource->extra_markup_amount > 0) {
            $netAmount += $this->resource->extra_markup_amount;
        }

        if ($this->resource->discount_amount > 0) {
            $netAmount -= $this->resource->discount_amount;
        }

        $netAmountAfterDiscount = $netAmount;

        // if ($this->resource->cgst_percentage > 0) {
        //     $netAmount += $netAmountAfterDiscount *  $this->resource->cgst_percentage * 0.01;
        // }

        // if ($this->resource->sgst_percentage > 0) {
        //     $netAmount += $netAmountAfterDiscount *  $this->resource->sgst_percentage * 0.01;
        // }

        // if ($this->resource->igst_percentage > 0) {
        //     $netAmount += $netAmountAfterDiscount *  $this->resource->igst_percentage * 0.01;
        // }

        // if ($this->resource->tcs_percentage > 0) {
        //     $netAmount += $netAmountAfterDiscount *  $this->resource->tcs_percentage * 0.01;
        // }

        $totalPercentage = $this->resource->cgst_percentage + $this->resource->sgst_percentage + $this->resource->igst_percentage + $this->resource->tcs_percentage;

        if ($totalPercentage > 0) {
            $netAmount += $netAmountAfterDiscount *  $totalPercentage * 0.01;
        }

        $perPersonAmounts = [];
            $mode = $this->resource->price_mode ?? 'PER_PERSON';
            // if ($mode == 'PER_PERSON') {
            $adultCount = $this->resource?->enquiry?->adult_count ?? 0;
            $childCount = $this->resource?->enquiry?->child_count  ?? 0;
            // $this->resource?->enquiry?->infant_count ?? 0;

            $adultPerPersonAmount = 0;
            $adulltNetAmount = 0;

            $chilPerPersonAmount = 0;
            $childNetAmount = 0;
            
            
            $adulltPerPersonNetAmount = 0;
            $childWPerPersonNetAmount = 0;
            $childNPerPersonNetAmount = 0;
            $childWPerPersonAmount = 0;
            $childNPerPersonAmount = 0;

            $perPersonAmounts = [];
            foreach ($this->resource->entries as $key => $entry) {
                if($entry->entry_type=='HOTEL'){
                    // Use pre-loaded room relation — no extra query
                    $room = $entry->room;
                    if ($room) {
                        $pricing = $room->calculateStayPricing($entry->start_date, $entry->end_date, $entry->toArray());
                        $adulltPerPersonNetAmount += $pricing['adult_net'];
                        $childWPerPersonNetAmount += $pricing['child_w_net'];
                        $childNPerPersonNetAmount += $pricing['child_n_net'];
                    }
                }
                
                if($entry->entry_type=='TRANSFER'){
                    $transferCost = $entry->transfer_type == 'PRIVATE' ? $entry->cost : $entry->adult_cost;
                    $adulltPerPersonNetAmount += $transferCost;
                    $childWPerPersonNetAmount += $transferCost;
                    $childNPerPersonNetAmount += $transferCost;
                }

                if($entry->entry_type=='ACTIVITY'){
                    // Use pre-loaded activity relation — no extra query
                    $activityEstimations = $entry->activity?->estimations ?? collect();
                    $activityStartDate = $entry['start_date'];
                    $activityEndDate = $entry['end_date'];
                    $activityEstimation = $activityEstimations
                        ->filter(fn($e) => $e->from_date <= $activityStartDate && $e->to_date >= $activityEndDate)
                        ->first();
                    $adulltPerPersonNetAmount += $activityEstimation?->adult_cost;
                    $childWPerPersonNetAmount += $activityEstimation?->child_cost;
                    $childNPerPersonNetAmount += $activityEstimation?->child_cost;
                }
            }

            if ($adultCount != 0) {
                $adultPerPersonAmount = $adulltPerPersonNetAmount / $adultCount;
            }

            if ($childCount != 0) {
                $childWPerPersonAmount = $childWPerPersonNetAmount / $childCount;
                $childNPerPersonAmount = $childNPerPersonNetAmount / $childCount;
            }

            // $perPersonAmounts  = [
            //     'adult' => $adultPerPersonAmount,
            //     'child' => $chilPerPersonAmount
            // ];
        // }

        if ($mode == 'PER_PERSON') {
            $perPersonAmounts  = [
                'adult' => $adultPerPersonAmount,
                'child_w' => $childWPerPersonAmount,
                'child_n' => $childNPerPersonAmount
            ];
        }else{
            $perPersonAmounts  = [
                'adult' => $adultPerPersonAmount,
                'child_w' => $childWPerPersonAmount,
                'child_n' => $childNPerPersonAmount
            ];
        }

        $editedBy = null;
        if ($this->resource->updater) {
            $editedBy = trim($this->resource->updater->first_name . ' ' . $this->resource->updater->last_name);
        } elseif ($this->resource->creator) {
            $editedBy = trim($this->resource->creator->first_name . ' ' . $this->resource->creator->last_name);
        } elseif ($this->resource->enquiry && $this->resource->enquiry->assigned_to_user) {
            $user = $this->resource->enquiry->assigned_to_user;
            $editedBy = trim($user->first_name . ' ' . $user->last_name);
        }

        return [
            'id' => $this->resource->id,
            'seq' => $this->resource->seq,
            'package_name' => $this->resource->package_name,
            'enquiry_id' => $this->resource->enquiry_id,
            'enquiry_ref_no' => $this->resource->enquiry ? $this->resource->enquiry->ref_no : null,
            'edited_by' => $editedBy,
            'enquiry' => EnquiryResource::make($this->resource->enquiry),
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
            'adult_count' => $this->resource->adult_count,
            'child_count' => $this->resource->child_count,
            'destination_id' => $this->resource->destination_id,
            'destination' => $this->resource->destination,
            'valid_until' => $this->resource->valid_until,
            'extra_markup_amount' => $this->resource->extra_markup_amount,
            'extra_markup_percentage' => $this->resource->extra_markup_percentage,
            'cgst_percentage' => $this->resource->cgst_percentage,
            'sgst_percentage' => $this->resource->sgst_percentage,
            'igst_percentage' => $this->resource->igst_percentage,
            'tcs_percentage' => $this->resource->tcs_percentage,
            'discount_amount' => $this->resource->discount_amount,
            'grand_total' => $this->resource->grand_total,
            'converted_total' => $this->resource->converted_total,
            'total_amount' => $this->resource->total_amount,
            'exchange_rate' => $this->resource->exchange_rate,
            'currency' => $this->resource->currency,
            'currency_code' => optional($this->resource->currency_obj)->code,
            'description' => $this->resource->description,
            'entries' => ItineraryEntryResource::collection($this->resource->entries),
            'net_amount' => $netAmount,
            'per_person_amounts' => $perPersonAmounts,
            'price_mode' => $this->resource->price_mode,
            'quoted_options' => $this->resource->quoted_options,
            'version' => $this->resource->version ?? 1,
            'is_current' => (bool) ($this->resource->is_current ?? true),
            'parent_itinerary_id' => $this->resource->parent_itinerary_id,
            'edited_at' => $this->resource->updated_at ? $this->resource->updated_at->toIso8601String() : null,
            'booking_status' => $this->resource->booking_status ?? 'pending',
            'booking_status_updated_at' => optional($this->resource->booking_status_updated_at)->toIso8601String(),
            'tour_acknowledgement_data' => is_string($this->resource->tour_acknowledgement_data) ? json_decode($this->resource->tour_acknowledgement_data, true) : $this->resource->tour_acknowledgement_data,
        ];
    }
}
