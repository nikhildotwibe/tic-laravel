<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\ActivityType;

class ActivityTypeController extends BaseController
{
    public function index()
    {
        try {
            $types = ActivityType::all();
            return $this->sendResponse($types, 'Activity Types Fetched', 200);
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

            $type = ActivityType::create(['name' => $request->name]);
            return $this->sendResponse($type, 'Activity Type Created', 201);
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

            $type = ActivityType::findOrFail($id);
            $type->name = $request->name;
            $type->save();
            return $this->sendResponse($type, 'Activity Type Updated', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function destroy($id)
    {
        try {
            ActivityType::findOrFail($id)->delete();
            return $this->sendResponse([], 'Activity Type Deleted', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
