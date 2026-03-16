<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\RoomAmenity;

class RoomAmenitiesController extends BaseController
{

    public function index()
    {
        try {
            $roomAmenities = RoomAmenity::latest()->get();
            return $this->sendResponse($roomAmenities, 'All Room Amenities Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:room_amenities,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $roomAmenity = new RoomAmenity();
            $roomAmenity->name = $request->name;
            $roomAmenity->save();

            return $this->sendResponse($roomAmenity, 'Room Amenity created Successfully', 201);
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
            $roomAmenity = RoomAmenity::findOrFail($id);
            return $this->sendResponse($roomAmenity, 'Room Amenity Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $roomAmenity = RoomAmenity::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:room_amenities,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();

            $roomAmenity->name = $request->name;
            $roomAmenity->update();

            return $this->sendResponse($roomAmenity, 'Room Amenity Updated', 200);
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
            RoomAmenity::findOrFail($id)->delete();
            return $this->sendResponse([], 'Room Amenity Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
