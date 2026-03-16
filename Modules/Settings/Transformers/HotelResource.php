<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
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
            'destination_id' => $this->resource->destination_id ?? "",
            'destination_name' => $this->resource->destination->name ?? "",
            'sub_destination_id' => $this->resource->sub_destination_id ?? "",
            'sub_destination_name' => $this->resource->sub_destination->name ?? "",
            'category_id' => $this->resource->category_id ?? "",
            'category_name' => $this->resource->category->name ?? "",
            'property_type_id' => $this->resource->property_type_id ?? "",
            'property_type_name' => $this->resource->property_type->name ?? "",
            'sales_email' => $this->resource->sales_email ?? "",
            'sales_no' => $this->resource->sales_no ?? "",
            'contact_no' => $this->resource->contact_no ?? "",
            'reservation_no' => $this->resource->reservation_no ?? "",
            'reservation_email' => $this->resource->reservation_email ?? "",
            'address' => $this->resource->address ?? "",
            'place' => $this->resource->place ?? "",
            'phone_number' => $this->resource->phone_number ?? "",
            'amenities' => AmenityResource::collection($this->resource->amenities),
            'rooms' => RoomResource::collection($this->resource->rooms),
            'document_1' => MediaResource::make(
                $this->resource->media->where('collection_name', 'hotel-profile-images')->first()
            ),
            'document_2' => MediaResource::collection(
                $this->resource->media->where('collection_name', 'hotel-images')
            ),
            'document_3' => MediaResource::collection(
                $this->resource->media->where('collection_name', 'hotel-documents-3')
            ),
            'document_4' => MediaResource::collection(
                $this->resource->media->where('collection_name', 'hotel-documents-4')
            ),

        ];
    }
}
