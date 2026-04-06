<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Customer;
use Modules\Settings\Entities\Transfer;
use Modules\Settings\Entities\TransferEstimation;
use Modules\Settings\Transformers\CustomerResource;
use Modules\Settings\Transformers\TransferResource;

class CustomerController extends BaseController
{



    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function searchByMobile(Request $request)
    {
        try {
            $customer = Customer::query()->latest();
            if (request()->has('mobile') && $request->mobile != '') {
                $customer = $customer->where('mobile', 'LIKE', '%' . $request->mobile . '%');
            }
            $customers = $customer->get();
            return $this->sendResponse(CustomerResource::collection($customers), 'Customers searched by mobile', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
