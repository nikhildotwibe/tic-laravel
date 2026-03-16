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
use Modules\Settings\Entities\SubDestination;

class DestinationsController extends BaseController
{

    public function index()
    {

        try {
            $destinations = Destination::latest()->get();
            return $this->sendResponse($destinations, 'All Destinations Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:destinations,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $destination = new Destination();
            $destination->name = $request->name;
            $destination->save();

            return $this->sendResponse($destination, 'Destination created Successfully', 201);
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
            $destination = Destination::findOrFail($id);
            return $this->sendResponse($destination, 'Destination Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $destination = Destination::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:destinations,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $destination->name = $request->name;
            $destination->update();

            return $this->sendResponse($destination, 'Destination Updated', 200);
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
            if (SubDestination::where('destination_id', $id)->count() == 0) {
                Destination::findOrFail($id)->delete();
                return $this->sendResponse([], 'Destination Deleted Successfully', 200);
            } else {
                return $this->sendResponse([], 'This Destination has sub destinations in it , please delete those first', 422);
            }
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
