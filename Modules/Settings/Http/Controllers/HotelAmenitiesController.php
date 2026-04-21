<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\HotelAmenity;

class HotelAmenitiesController extends BaseController
{

    public function index()
    {
        try {
            $hotelAmenities = HotelAmenity::latest()->get();
            return $this->sendResponse($hotelAmenities, 'All Hotel Amenities Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:hotel_amenities,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $hotelAmenity = new HotelAmenity();
            $hotelAmenity->name = $request->name;
            $hotelAmenity->save();

            return $this->sendResponse($hotelAmenity, 'Hotel Amenity created Successfully', 201);
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
            $hotelAmenity = HotelAmenity::findOrFail($id);
            return $this->sendResponse($hotelAmenity, 'Hotel Amenity Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $hotelAmenity = HotelAmenity::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:hotel_amenities,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();

            $hotelAmenity->name = $request->name;
            $hotelAmenity->update();

            return $this->sendResponse($hotelAmenity, 'Hotel Amenity Updated', 200);
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
            HotelAmenity::findOrFail($id)->delete();
            return $this->sendResponse([], 'Hotel amenity Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
