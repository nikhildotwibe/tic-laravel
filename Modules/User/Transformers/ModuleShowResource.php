<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ModuleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $moduleResource = new ModuleResource($this->resource);

        return Arr::collapse(
            [
                $moduleResource->toArray($request),
                [
                    'permissions' => [
                        'read' => PermissionResource::collection($this->resource->read_permissions),
                        'write' => PermissionResource::collection($this->resource->write_permissions),
                        'update' => PermissionResource::collection($this->resource->update_permissions),
                        'delete' => PermissionResource::collection($this->resource->delete_permissions),
                    ]
                ]
            ],
        );
    }
}
