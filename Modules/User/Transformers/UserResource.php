<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'username' => $this->resource->username,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'dob' => $this->resource->dob,
            'gender' => $this->resource->gender,
            'start_date' => $this->resource->start_date,
            'end_date' => $this->resource->end_date,
            'role_id' => $this->resource->role_id,
            'roles' => RoleShowResource::collection($this->resource->roles),
            'profile_picture' => $this->resource->getFirstMediaUrl(MEDIA_PROFILE_IMAGES),
            'address' => $this->resource->address,
            'language' => $this->resource->language,
            'language_name' => optional($this->resource->languages)->language,
            'country_id' => $this->resource->country,
            'country' => $this->resource->countryRelation,
            'is_super_admin' => $this->resource->roles()->pluck('name')->contains('super admin')
        ];
    }
}
