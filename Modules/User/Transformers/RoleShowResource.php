<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'permissions' => PermissionResource::collection($this->resource->permissions),
            'created_date' => date('Y-m-d h:i:s', strtotime($this->resource->created_at)),
            'updated_date' => date('Y-m-d h:i:s', strtotime($this->resource->updated_at)),
        ];
    }
}
