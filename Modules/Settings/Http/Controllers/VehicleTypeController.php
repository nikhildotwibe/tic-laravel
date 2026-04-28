<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\VehicleType;

class VehicleTypeController extends BaseController
{
    public function index()
    {
        try {
            $types = VehicleType::all();
            return $this->sendResponse($types, 'Vehicle Types Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ])->validate();

            $type = VehicleType::create(['name' => $request->name]);
            return $this->sendResponse($type, 'Vehicle Type Created', 201);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function show($id)
    {
        try {
            $type = VehicleType::findOrFail($id);
            return $this->sendResponse($type, 'Vehicle Type Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ])->validate();

            $type = VehicleType::findOrFail($id);
            $type->name = $request->name;
            $type->save();
            return $this->sendResponse($type, 'Vehicle Type Updated', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function destroy($id)
    {
        try {
            VehicleType::findOrFail($id)->delete();
            return $this->sendResponse([], 'Vehicle Type Deleted', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
