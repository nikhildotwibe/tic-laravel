<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Requirement;
use Modules\Settings\Transformers\RequirementResource;

class RequirementsController extends BaseController
{

    public function index()
    {
        try {
            $requirement = Requirement::latest()->get();
            return $this->sendResponse(RequirementResource::collection($requirement), 'All Requirements Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:requirements,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $requirement = new Requirement();
            $requirement->name = $request->name;
            $requirement->save();

            return $this->sendResponse(RequirementResource::make($requirement), 'Requirement created Successfully', 201);
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
            $requirement = Requirement::findOrFail($id);
            return $this->sendResponse(RequirementResource::make($requirement), 'Requirement Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $requirement = Requirement::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:requirements,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $requirement->name = $request->name;
            $requirement->update();

            return $this->sendResponse(RequirementResource::make($requirement), 'Requirement Updated', 200);
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
            Requirement::findOrFail($id)->delete();
            return $this->sendResponse([], 'Requirement Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
