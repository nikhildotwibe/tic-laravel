<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\MarketType;
use Modules\Settings\Entities\PropertyType;

class MarketTypesController extends BaseController
{

    public function index()
    {
        try {
            $marketTypes = MarketType::latest()->get();
            return $this->sendResponse($marketTypes, 'All Market Types Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:market_types,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $marketType = new MarketType();
            $marketType->name = $request->name;
            $marketType->save();

            return $this->sendResponse($marketType, 'Market Type created Successfully', 201);
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
            $marketType = MarketType::findOrFail($id);
            return $this->sendResponse($marketType, 'Market Type Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $marketType = MarketType::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:market_types,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $marketType->name = $request->name;
            $marketType->update();

            return $this->sendResponse($marketType, 'Market Type Updated', 200);
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
            MarketType::findOrFail($id)->delete();
            return $this->sendResponse([], 'Market Type Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
