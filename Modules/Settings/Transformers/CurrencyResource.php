<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
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
            'symbol' => $this->resource->symbol,
            'code' => $this->resource->code,
            'exchange_rate' => $this->resource->exchange_rate,
            'currency_format' => $this->resource->currency_format,
            'from_currency' => $this->resource->from_currency,
            'to_currency' => $this->resource->to_currency,
        ];
    }
}
