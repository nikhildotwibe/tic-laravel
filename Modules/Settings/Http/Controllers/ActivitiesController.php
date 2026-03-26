<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Activity;
use Modules\Settings\Entities\ActivityEstimation;
use Modules\Settings\Transformers\ActivityResource;

class ActivitiesController extends BaseController
{

    public function index()
    {

        try {
            $activity = Activity::latest()->get();
            return $this->sendResponse(ActivityResource::collection($activity), 'All Activities Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'activity_name' => 'required',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'sub_destination_id' => 'nullable|exists:sub_destinations,id,deleted_at,NULL',
                'contact_number' => 'required',
                'contact_email' => 'nullable|email',
                'is_active' => 'required|in:0,1',
                'activity_type_id' => 'nullable|exists:activity_types,id,deleted_at,NULL',
                'adult_count' => 'nullable|integer|min:0',
                'child_count' => 'nullable|integer|min:0',
                'estimations' => 'required|array',
                'estimations.*.from_date' => 'required|date_format:Y-m-d',
                'estimations.*.to_date' => 'required|date_format:Y-m-d',
                'estimations.*.opening_time' => 'nullable|date_format:H:i:s',
                'estimations.*.closing_time' => 'required|date_format:H:i:s',
                'estimations.*.adult_cost' => 'required|gte:0',
                'estimations.*.child_cost' => 'required|gte:0',
            ])->validate();

            $activity = new Activity();
            $activity->activity_name = $request->activity_name;
            $activity->destination_id = $request->destination_id;
            $activity->sub_destination_id = $request->sub_destination_id;
            $activity->contact_number = $request->contact_number;
            $activity->contact_email = $request->contact_email;
            $activity->description = $request->description;
            $activity->is_active = $request->is_active;
            $activity->activity_type_id = $request->activity_type_id;
            $activity->adult_count = $request->adult_count ?? 0;
            $activity->child_count = $request->child_count ?? 0;
            $activity->save();

            if (!empty($request->image)) {
                $activity->addMediaFromRequest('image')->toMediaCollection('activity-images');
            }

            foreach ($request->estimations as $estimationData) {
                $estimation = new ActivityEstimation();
                $estimation->activity_id = $activity->id;
                $estimation->from_date = $estimationData['from_date'];
                $estimation->to_date = $estimationData['to_date'];
                $estimation->opening_time = $estimationData['opening_time'];
                $estimation->closing_time = $estimationData['closing_time'];
                $estimation->adult_cost = $estimationData['adult_cost'];
                $estimation->child_cost = $estimationData['child_cost'];
                $estimation->save();
            }

            return $this->sendResponse(ActivityResource::make($activity), 'Activity created Successfully', 201);
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
            $activity = Activity::findOrFail($id);
            return $this->sendResponse(ActivityResource::make($activity), 'Activity Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $activity = Activity::findOrFail($id);

            Validator::make($request->all(), [
                'activity_name' => 'required',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'sub_destination_id' => 'nullable|exists:sub_destinations,id,deleted_at,NULL',
                'contact_number' => 'required',
                'contact_email' => 'nullable|email',
                'is_active' => 'required|in:0,1',
                'activity_type_id' => 'nullable|exists:activity_types,id,deleted_at,NULL',
                'adult_count' => 'nullable|integer|min:0',
                'child_count' => 'nullable|integer|min:0',
                'estimations' => 'required|array',
                'estimations.*.from_date' => 'required|date_format:Y-m-d',
                'estimations.*.to_date' => 'required|date_format:Y-m-d',
                'estimations.*.opening_time' => 'nullable|date_format:H:i:s',
                'estimations.*.closing_time' => 'required|date_format:H:i:s',
                'estimations.*.adult_cost' => 'required|gte:0',
                'estimations.*.child_cost' => 'required|gte:0',
            ])->validate();

            $activity->activity_name = $request->activity_name;
            $activity->destination_id = $request->destination_id;
            $activity->sub_destination_id = $request->sub_destination_id;
            $activity->contact_number = $request->contact_number;
            $activity->contact_email = $request->contact_email;
            $activity->description = $request->description;
            $activity->is_active = $request->is_active;
            $activity->activity_type_id = $request->activity_type_id;
            $activity->adult_count = $request->adult_count ?? 0;
            $activity->child_count = $request->child_count ?? 0;
            $activity->save();

            if (!empty($request->image)) {
                $activity->addMediaFromRequest('image')->toMediaCollection('transfer-images');
            }

            $savedItems = [];

            foreach ($request->estimations as $estimationData) {
                if (!empty($estimationData['id'])) {
                    $estimation = ActivityEstimation::findOrFail($estimationData['id']);
                } else {
                    $estimation = new ActivityEstimation();
                }
                $estimation->activity_id = $activity->id;
                $estimation->from_date = $estimationData['from_date'];
                $estimation->to_date = $estimationData['to_date'];
                $estimation->opening_time = $estimationData['opening_time'];
                $estimation->closing_time = $estimationData['closing_time'];
                $estimation->adult_cost = $estimationData['adult_cost'];
                $estimation->child_cost = $estimationData['child_cost'];
                $estimation->save();
                $savedItems[] = $estimation;
            }

            ActivityEstimation::where('activity_id', $id)->whereNotIn('id', collect($savedItems)->pluck('id'))->delete();


            return $this->sendResponse(ActivityResource::make($activity), 'Activity Updated', 200);
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
            Activity::findOrFail($id)->delete();
            return $this->sendResponse([], 'Activity Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            Validator::make($request->all(), [
                'is_active' => 'required|in:0,1',
            ])->validate();
            $activity = Activity::findOrFail($id);
            $activity->is_active = $request->is_active;
            $activity->save();
            return $this->sendResponse(ActivityResource::make($activity), 'Activity Status updated Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
