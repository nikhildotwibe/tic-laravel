<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Currency;
use Modules\Settings\Transformers\CurrencyResource;

class CurrencyController extends BaseController
{

    public function index()
    {
        try {
            $currency = Currency::latest()->get();
            return $this->sendResponse(CurrencyResource::collection($currency), 'All Currencies Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:currencies,name,NULL,id,deleted_at,NULL',
                'symbol' => 'nullable|string',
                'code' => 'nullable|string',
                'exchange_rate' => 'nullable|string',
                'currency_format' => 'nullable|string',
                'from_currency' => 'nullable|string',
                'to_currency' => 'nullable|string',
            ])->validate();

            $currency = new Currency();
            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->code = $request->code;
            $currency->exchange_rate = $request->exchange_rate;
            $currency->currency_format = $request->currency_format;
            $currency->from_currency = $request->from_currency;
            $currency->to_currency = $request->to_currency;
            $currency->save();

            return $this->sendResponse(CurrencyResource::make($currency), 'Currency created Successfully', 201);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }



    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            return $this->sendResponse(CurrencyResource::make($currency), 'Currency Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $currency = Currency::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:currencies,name,' . $id . ',id,deleted_at,NULL',
                'symbol' => 'nullable|string',
                'code' => 'nullable|string',
                'exchange_rate' => 'nullable|string',
                'currency_format' => 'nullable|string',
                'from_currency' => 'nullable|string',
                'to_currency' => 'nullable|string',
            ])->validate();


            $currency->name = $request->name;
            $currency->symbol = $request->symbol;
            $currency->code = $request->code;
            $currency->exchange_rate = $request->exchange_rate;
            $currency->currency_format = $request->currency_format;
            $currency->from_currency = $request->from_currency;
            $currency->to_currency = $request->to_currency;
            $currency->update();

            return $this->sendResponse(CurrencyResource::make($currency), 'Currency Updated', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            Currency::findOrFail($id)->delete();
            return $this->sendResponse([], 'Currency Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
