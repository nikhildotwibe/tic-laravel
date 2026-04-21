<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Supplier;
use Modules\Settings\Entities\Transfer;
use Modules\Settings\Entities\TransferEstimation;
use Modules\Settings\Transformers\SupplierResource;
use Modules\Settings\Transformers\TransferResource;

class SupplierController extends BaseController
{



    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function searchByMobile(Request $request)
    {
        try {
            $supplier = Supplier::query()->latest();
            if (request()->has('mobile') && $request->mobile != '') {
                $supplier = $supplier->where('mobile', 'LIKE', '%' . $request->mobile . '%');
            }
            $suppliers = $supplier->get();
            return $this->sendResponse(SupplierResource::collection($suppliers), 'Suppliers searched by mobile', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
