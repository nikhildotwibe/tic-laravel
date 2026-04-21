<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Priority;
use Modules\Settings\Transformers\PriorityResource;

class PriorityController extends BaseController
{

    public function index()
    {
        try {
            $priority = Priority::latest()->get();
            return $this->sendResponse(PriorityResource::collection($priority), 'All Priorities Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:priorities,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $priority = new Priority();
            $priority->name = $request->name;
            $priority->save();

            return $this->sendResponse(PriorityResource::make($priority), 'Priority created Successfully', 201);
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
            $priority = Priority::findOrFail($id);
            return $this->sendResponse(PriorityResource::make($priority), 'Priority Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $priority = Priority::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:priorities,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $priority->name = $request->name;
            $priority->update();

            return $this->sendResponse(PriorityResource::make($priority), 'Priority Updated', 200);
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
            Priority::findOrFail($id)->delete();
            return $this->sendResponse([], 'Priority Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
