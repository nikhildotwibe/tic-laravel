<?php

namespace Modules\User\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $userResource = new UserResource($this->resource);

        return Arr::collapse(
            [
                $userResource->toArray($request),
                []
            ]
        );
    }
}
