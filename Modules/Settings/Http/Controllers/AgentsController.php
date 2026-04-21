<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Agent;
use Modules\Settings\Transformers\AgentResource;

class AgentsController extends BaseController
{

    public function index()
    {

        try {
            $agents = Agent::latest()->get();
            return $this->sendResponse(AgentResource::collection($agents), 'All Agents Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:agents,name,NULL,id,deleted_at,NULL',
                'phone' => 'required|unique:agents,phone,NULL,id,deleted_at,NULL',
                'email' => 'nullable|email|unique:agents,email,NULL,id,deleted_at,NULL',
                'country_id' => 'exists:countries,id,deleted_at,NULL',
            ])->validate();

            $agent = new Agent();
            $agent->name = $request->name;
            $agent->phone = $request->phone;
            $agent->email = $request->email;
            $agent->country_id = $request->country_id;
            $agent->address = $request->address;
            $agent->save();

            return $this->sendResponse(AgentResource::make($agent), 'Agent created Successfully', 201);
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
            $agent = Agent::findOrFail($id);
            return $this->sendResponse(AgentResource::make($agent), 'Agent Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $agent = Agent::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:agents,name,' . $id . ',id,deleted_at,NULL',
                'phone' => 'required|unique:agents,phone,' . $id . ',id,deleted_at,NULL',
                'email' => 'nullable|email|unique:agents,email,' . $id . ',id,deleted_at,NULL',
                'country_id' => 'exists:countries,id,deleted_at,NULL',
            ])->validate();

            $agent->name = $request->name;
            $agent->phone = $request->phone;
            $agent->email = $request->email;
            $agent->country_id = $request->country_id;
            $agent->address = $request->address;
            $agent->update();

            return $this->sendResponse(AgentResource::make($agent), 'Agent Updated', 200);
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
            Agent::findOrFail($id)->delete();
            return $this->sendResponse([], 'Agent Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
