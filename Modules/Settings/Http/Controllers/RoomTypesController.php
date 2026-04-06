<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\RoomType;

class RoomTypesController extends BaseController
{

    public function index()
    {
        try {
            $roomTypes = RoomType::latest()->get();
            return $this->sendResponse($roomTypes, 'All Room Types Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:room_types,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $roomType = new RoomType();
            $roomType->name = $request->name;
            $roomType->save();

            return $this->sendResponse($roomType, 'Room Type created Successfully', 201);
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
            $roomType = RoomType::findOrFail($id);
            return $this->sendResponse($roomType, 'Room Type Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $roomType = RoomType::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:room_types,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $roomType->name = $request->name;
            $roomType->update();

            return $this->sendResponse($roomType, 'Room Type Updated', 200);
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
            RoomType::findOrFail($id)->delete();
            return $this->sendResponse([], 'Room Type Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
