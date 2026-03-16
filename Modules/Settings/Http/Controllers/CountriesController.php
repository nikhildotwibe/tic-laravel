<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\Country;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CountriesController extends BaseController
{

    public function index()
    {

        try {
            $countries = Country::latest()->get();
            return $this->sendResponse($countries, 'All Countries Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:countries,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $country = new Country();
            $country->name = $request->name;
            $country->save();

            return $this->sendResponse($country, 'Country created Successfully', 201);
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
        // return view('settings::show');
        try {
            $country = Country::findOrFail($id);
            return $this->sendResponse($country, 'Country Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $country = Country::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:countries,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $country->name = $request->name;
            $country->save();
            return $this->sendResponse($country, 'Country Updated', 200);
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
            Country::findOrFail($id)->delete();
            return $this->sendResponse([], 'Country Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
