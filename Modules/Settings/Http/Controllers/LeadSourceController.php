<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\LeadSource;
use Modules\Settings\Transformers\LeadSourceResource;

class LeadSourceController extends BaseController
{

    public function index()
    {
        try {
            $leadSources = LeadSource::latest()->get();
            return $this->sendResponse(LeadSourceResource::collection($leadSources), 'All Lead Sources Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:lead_sources,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $leadSource = new LeadSource();
            $leadSource->name = $request->name;
            $leadSource->save();

            return $this->sendResponse(LeadSourceResource::make($leadSource), 'Lead Source created Successfully', 201);
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
            $leadSource = LeadSource::findOrFail($id);
            return $this->sendResponse(LeadSourceResource::make($leadSource), 'Lead Source Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $leadSource = LeadSource::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:lead_sources,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $leadSource->name = $request->name;
            $leadSource->update();

            return $this->sendResponse(LeadSourceResource::make($leadSource), 'Lead Source Updated', 200);
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
            LeadSource::findOrFail($id)->delete();
            return $this->sendResponse([], 'Lead Source Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
