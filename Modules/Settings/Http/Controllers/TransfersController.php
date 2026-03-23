<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Transfer;
use Modules\Settings\Entities\TransferEstimation;
use Modules\Settings\Transformers\TransferResource;

class TransfersController extends BaseController
{

    public function index()
    {

        try {
            $transfers = Transfer::latest()->get();
            return $this->sendResponse(TransferResource::collection($transfers), 'All Transfers Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'vehicle_name' => 'required',
                'vehicle_number' => 'required',
                'pickuppoint' => 'nullable|string',
                'droppoint' => 'nullable|string',
                'destination_id' => 'nullable|exists:destinations,id,deleted_at,NULL',
                'phone_number' => 'required',
                'is_active' => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
                'estimations' => 'required|array',
                'estimations.*.from_date' => 'required|date_format:Y-m-d',
                'estimations.*.to_date' => 'required|date_format:Y-m-d',
                'estimations.*.type' => 'required|in:Private,SIC',
                'estimations.*.vehicletype' => 'nullable|string',
                'estimations.*.cost' => 'nullable|gte:0',
                'estimations.*.adult_cost' => 'nullable|gte:0',
                'estimations.*.child_cost' => 'nullable|gte:0',
            ])->validate();

            $transfer = new Transfer();
            $transfer->vehicle_name = $request->vehicle_name;
            $transfer->vehicle_number = $request->vehicle_number;
            $transfer->pickuppoint = $request->pickuppoint;
            $transfer->droppoint = $request->droppoint;
            $transfer->destination_id = $request->destination_id;
            $transfer->phone_number = $request->phone_number;
            $transfer->description = $request->description;
            $transfer->is_active = $request->is_active;
            $transfer->save();

            if (!empty($request->image)) {
                $transfer->clearMediaCollection('transfer-images');
                $transfer->addMediaFromRequest('image')->toMediaCollection('transfer-images');
            }

            foreach ($request->estimations as $estimationData) {
                $estimation = new TransferEstimation();
                $estimation->transfer_id = $transfer->id;
                $estimation->from_date = $estimationData['from_date'];
                $estimation->to_date = $estimationData['to_date'];
                $estimation->type = $estimationData['type'];
                $estimation->vehicletype = $estimationData['vehicletype'] ?? null;
                $estimation->cost = $estimationData['cost'];
                $estimation->adult_cost = $estimationData['adult_cost'];
                $estimation->child_cost = $estimationData['child_cost'];
                $estimation->save();
            }

            return $this->sendResponse(TransferResource::make($transfer), 'Transfer created Successfully', 201);
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
            $transfer = Transfer::findOrFail($id);
            return $this->sendResponse(TransferResource::make($transfer), 'Transfer Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $transfer = Transfer::findOrFail($id);

            Validator::make($request->all(), [
                'vehicle_name' => 'required',
                'vehicle_number' => 'required',
                'pickuppoint' => 'nullable|string',
                'droppoint' => 'nullable|string',
                'destination_id' => 'nullable|exists:destinations,id,deleted_at,NULL',
                'phone_number' => 'required',
                'is_active' => 'required|in:0,1',
                'estimations' => 'required|array',
                'estimations.*.id' => 'nullable|exists:transfer_estimations,id,deleted_at,NULL',
                'estimations.*.from_date' => 'required|date_format:Y-m-d',
                'estimations.*.to_date' => 'required|date_format:Y-m-d',
                'estimations.*.type' => 'required|in:Private,SIC',
                'estimations.*.vehicletype' => 'nullable|string',
                'estimations.*.cost' => 'nullable|gte:0',
                'estimations.*.adult_cost' => 'nullable|gte:0',
                'estimations.*.child_cost' => 'nullable|gte:0',
            ])->validate();

            $transfer->vehicle_name = $request->vehicle_name;
            $transfer->vehicle_number = $request->vehicle_number;
            $transfer->pickuppoint = $request->pickuppoint;
            $transfer->droppoint = $request->droppoint;
            $transfer->destination_id = $request->destination_id;
            $transfer->phone_number = $request->phone_number;
            $transfer->description = $request->description;
            $transfer->is_active = $request->is_active;
            $transfer->save();

            if (!empty($request->image)) {
                $transfer->clearMediaCollection('transfer-images');
                $transfer->addMediaFromRequest('image')->toMediaCollection('transfer-images');
            }

            $savedItems = [];

            foreach ($request->estimations as $estimationData) {
                if (!empty($estimationData['id'])) {
                    $estimation = TransferEstimation::findOrFail($estimationData['id']);
                } else {
                    $estimation = new TransferEstimation();
                }
                $estimation->transfer_id = $transfer->id;
                $estimation->from_date = $estimationData['from_date'];
                $estimation->to_date = $estimationData['to_date'];
                $estimation->type = $estimationData['type'];
                $estimation->vehicletype = $estimationData['vehicletype'] ?? null;
                $estimation->cost = $estimationData['cost'];
                $estimation->adult_cost = $estimationData['adult_cost'];
                $estimation->child_cost = $estimationData['child_cost'];
                $estimation->save();
                $savedItems[] = $estimation;
            }

            TransferEstimation::where('transfer_id', $id)->whereNotIn('id', collect($savedItems)->pluck('id'))->delete();


            return $this->sendResponse(TransferResource::make($transfer), 'Transfer Updated', 200);
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
            Transfer::findOrFail($id)->delete();
            return $this->sendResponse([], 'Transfer Deleted Successfully', 200);
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
            $transfer = Transfer::findOrFail($id);
            $transfer->is_active = $request->is_active;
            $transfer->save();
            return $this->sendResponse(TransferResource::make($transfer), 'Transfer Status updated Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
