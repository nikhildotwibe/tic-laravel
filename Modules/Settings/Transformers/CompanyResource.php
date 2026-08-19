<?php

namespace Modules\Settings\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'country' => $this->resource->country,
            'address' => $this->resource->address,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'tax_no' => $this->resource->tax_no,
            'status' => $this->resource->status,
            'is_default' => (bool)$this->resource->is_default,
            'logoUrl' => $this->resource->getFirstMediaUrl('logo') ?: '',
            'headerUrl' => $this->resource->getFirstMediaUrl('header') ?: '',
            'footerUrl' => $this->resource->getFirstMediaUrl('footer') ?: '',
            'bankAccounts' => $this->resource->bankAccounts->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'bankName' => $bank->bank_name,
                    'accountName' => $bank->account_name,
                    'accountNumber' => $bank->account_number,
                    'swiftCode' => $bank->swift_code,
                    'branchName' => $bank->branch_name,
                    'isPrimary' => (bool)$bank->is_primary,
                ];
            }),
        ];
    }
}
