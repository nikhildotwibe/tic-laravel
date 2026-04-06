<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\SubDestination;
use Modules\Settings\Transformers\SubDestinationResource;

class SubDestinationsController extends BaseController
{

    public function index()
    {

        try {

            $query = SubDestination::query()->latest();

            if (request()->has('destination_id')) {
                $query = $query->where('destination_id', request()->destination_id);
            }

            $subDestinations = $query->get();

            return $this->sendResponse(SubDestinationResource::collection($subDestinations), 'All Sub Destinations Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'name' => 'required|unique:sub_destinations,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $subDestination = new subDestination();
            $subDestination->name = $request->name;
            $subDestination->destination_id = $request->destination_id;
            $subDestination->save();

            return $this->sendResponse($subDestination, 'Sub Destination created Successfully', 201);
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
            $subDestination = SubDestination::findOrFail($id);
            return $this->sendResponse($subDestination, 'Sub Destination Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $subDestination = SubDestination::findOrFail($id);

            Validator::make($request->all(), [
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'name' => 'required|unique:destinations,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $subDestination->name = $request->name;
            $subDestination->destination_id = $request->destination_id;
            $subDestination->update();

            return $this->sendResponse($subDestination, 'Sub Destination Updated', 200);
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
            SubDestination::findOrFail($id)->delete();
            return $this->sendResponse([], 'Sub Destination Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
