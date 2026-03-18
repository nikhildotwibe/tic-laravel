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
         'file_url' => url(
    str_contains($this->resource->getUrl(), '/storage/')
        ? str_replace('/storage/', '/uploads/', $this->resource->getUrl())
        : $this->resource->getUrl()
),
            'mime_type' => $this->resource->mime_type,
            'size' => $this->resource->size,
            'human_readable_size' => $this->resource->human_readable_size,
        ];
    }
}
