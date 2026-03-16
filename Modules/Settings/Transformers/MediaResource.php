<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'file_name' => $this->resource->file_name,
            'file_url' => $this->resource->getFullUrl(),
            'mime_type' => $this->resource->mime_type,
            'size' => $this->resource->size,
            'human_readable_size' => $this->resource->human_readable_size,
        ];
    }
}
