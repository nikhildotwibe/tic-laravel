<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Destination;
use Modules\Settings\Entities\PropertyType;

class PropertyTypesController extends BaseController
{

    public function index()
    {
        try {
            $propertyTypes = PropertyType::latest()->get();
            return $this->sendResponse($propertyTypes, 'All Property Types Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:property_types,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $propertyType = new PropertyType();
            $propertyType->name = $request->name;
            $propertyType->save();

            return $this->sendResponse($propertyType, 'Property Type created Successfully', 201);
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
            $propertyType = PropertyType::findOrFail($id);
            return $this->sendResponse($propertyType, 'Property Type Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $propertyType = PropertyType::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:property_types,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $propertyType->name = $request->name;
            $propertyType->update();

            return $this->sendResponse($propertyType, 'Property Type Updated', 200);
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
            PropertyType::findOrFail($id)->delete();
            return $this->sendResponse([], 'Property Type Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
