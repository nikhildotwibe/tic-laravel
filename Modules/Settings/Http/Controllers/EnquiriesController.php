<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Settings\Entities\Customer;
use Modules\Settings\Entities\Enquiry;
use Modules\Settings\Entities\EnquiryRequirement;
use Modules\Settings\Entities\EnquirySubDestination;
use Modules\Settings\Transformers\EnquiryResource;

class EnquiriesController extends BaseController
{

    public function index()
    {

        try {
            $enquiry = Enquiry::latest()->get();
            return $this->sendResponse(EnquiryResource::collection($enquiry), 'All Enquiries Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {

            $rules = [
                'type' => 'required|in:B2B,B2C',
                'agent_id' => 'required_if:type,B2B|exists:agents,id,deleted_at,NULL',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                // 'sub_destination_id' => 'required|exists:sub_destinations,id,deleted_at,NULL',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'adult_count' => 'required|gte:0',
                'child_count' => 'required|gte:0',
                'infant_count' => 'required|gte:0',
                'lead_source_id' => 'required|exists:lead_sources,id,deleted_at,NULL',
                'priority_id' => 'required|exists:priorities,id,deleted_at,NULL',

                'assigned_to' => 'required|exists:users,id,deleted_at,NULL',

                'requirements' => 'required|array',
                'requirements.*' => 'required|exists:requirements,id,deleted_at,NULL',

                'sub_destinations' => 'required|array',
                'sub_destinations.*' => 'required|exists:sub_destinations,id,deleted_at,NULL',
            ];

            if ($request->type == 'B2B' && !request()->has('customer_id')) {
                $rules['name'] = 'required';
                $rules['email'] = 'required|email';
                $rules['mobile'] = 'required';
               
            }  elseif ($request->type == 'B2C' && !request()->filled('customer_id')) {
                $rules['name'] = 'required';
                $rules['email'] = 'required|email';
                $rules['mobile'] = 'required';
                $rules['salute'] = 'required|in:Mr,Ms';
            
            } elseif ($request->type == 'B2C' && request()->filled('customer_id')) {
                $rules['customer_id'] = 'required|exists:customers,id,deleted_at,NULL';
                $rules['salute'] = 'required|in:Mr,Ms';
            }

            Validator::make($request->all(), $rules, [
                'type.in' => 'Invalid type. Only possible values B2B, B2C',
                'salute.in' => 'Invalid salute. Only possible values Mr, Ms',
            ])->validate();


            $customerId = $request->customer_id;

            if ($request->type == 'B2C' && !request()->filled('customer_id')) {
                $customer = new Customer();
                $customer->salute = $request->salute;
                $customer->name = $request->name;
                $customer->email = $request->email;
                $customer->mobile = $request->mobile;
                $customer->description = $request->description;
                $customer->save();

                $customerId = $customer->id;
            }

            $enquiry = new Enquiry();
            $enquiry->type = $request->type;
            $enquiry->ref_no = $request->ref_no;
            $enquiry->agent_id = $request->agent_id;
            $enquiry->destination_id = $request->destination_id;
            // $enquiry->sub_destination_id = $request->sub_destination_id;
            $enquiry->start_date = $request->start_date;
            $enquiry->end_date = $request->end_date;
            $enquiry->adult_count = $request->adult_count;
            $enquiry->child_count = $request->child_count;
            $enquiry->infant_count = $request->infant_count;
            $enquiry->lead_source_id = $request->lead_source_id;
            $enquiry->priority_id = $request->priority_id;
            $enquiry->customer_id = $customerId;
            $enquiry->assigned_to = $request->assigned_to;

            $enquiry->save();

            foreach ($request->sub_destinations ?? [] as $key => $sub_destination) {
                $enquirySubDestination = new EnquirySubDestination();
                $enquirySubDestination->enquiry_id = $enquiry->id;
                $enquirySubDestination->sub_destination_id = $sub_destination;
                $enquirySubDestination->save();
            }

            foreach ($request->requirements ?? [] as $key => $req) {
                $enquiryRequirement = new EnquiryRequirement();
                $enquiryRequirement->enquiry_id = $enquiry->id;
                $enquiryRequirement->requirement_id = $req;
                $enquiryRequirement->save();
            }


            return $this->sendResponse(EnquiryResource::make($enquiry), 'Enquiry created Successfully', 201);
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
            $enquiry = Enquiry::findOrFail($id);
            return $this->sendResponse(EnquiryResource::make($enquiry), 'Enquiry Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'type' => 'required|in:B2B,B2C',
                'agent_id' => 'required_if:type,B2B|exists:agents,id,deleted_at,NULL',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                // 'sub_destination_id' => 'required|exists:sub_destinations,id,deleted_at,NULL',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'adult_count' => 'required|gte:0',
                'child_count' => 'required|gte:0',
                'infant_count' => 'required|gte:0',
                'lead_source_id' => 'required|exists:lead_sources,id,deleted_at,NULL',
                'priority_id' => 'required|exists:priorities,id,deleted_at,NULL',

                'customer_id' => 'nullable|exists:customers,id,deleted_at,NULL',
                'assigned_to' => 'required|exists:users,id,deleted_at,NULL',

                'requirements' => 'required|array',
                'requirements.*' => 'required|exists:requirements,id,deleted_at,NULL',
                
                'sub_destinations' => 'required|array',
                'sub_destinations.*' => 'required|exists:sub_destinations,id,deleted_at,NULL',
            ];

            if ($request->type == 'B2C' && !request()->has('customer_id')) {
                $rules['name'] = 'required';
                $rules['email'] = 'required|email';
                $rules['mobile'] = 'required';
                $rules['salute'] = 'required|in:Mr,Ms';
            } elseif ($request->type == 'B2C') {
                $rules['customer_id'] = 'required|exists:customers,id,deleted_at,NULL';
            }


            Validator::make($request->all(), $rules, [
                'type.in' => 'Invalid type. Only possible values B2B, B2C',
                'salute.in' => 'Invalid salute. Only possible values Mr, Ms',
            ])->validate();

            $enquiry = Enquiry::findOrFail($id);
            $enquiry->type = $request->type;
            $enquiry->ref_no = $request->ref_no;
            $enquiry->agent_id = $request->agent_id;
            $enquiry->destination_id = $request->destination_id;
            // $enquiry->sub_destination_id = $request->sub_destination_id;
            $enquiry->start_date = $request->start_date;
            $enquiry->end_date = $request->end_date;
            $enquiry->adult_count = $request->adult_count;
            $enquiry->child_count = $request->child_count;
            $enquiry->infant_count = $request->infant_count;
            $enquiry->lead_source_id = $request->lead_source_id;
            $enquiry->priority_id = $request->priority_id;
            $enquiry->customer_id = $request->customer_id;
            $enquiry->assigned_to = $request->assigned_to;
            $enquiry->save();

            EnquirySubDestination::where('enquiry_id', $id)->forceDelete();
            foreach ($request->sub_destinations ?? [] as $key => $sub_destination) {
                $enquirySubDestination = new EnquirySubDestination();
                $enquirySubDestination->enquiry_id = $enquiry->id;
                $enquirySubDestination->sub_destination_id = $sub_destination;
                $enquirySubDestination->save();
            }

            EnquiryRequirement::where('enquiry_id', $id)->forceDelete();
            foreach ($request->requirements ?? [] as $key => $req) {
                $enquiryRequirement = new EnquiryRequirement();
                $enquiryRequirement->enquiry_id = $enquiry->id;
                $enquiryRequirement->requirement_id = $req;
                $enquiryRequirement->save();
            }
            
            $enquiry = Enquiry::findOrFail($id);

            return $this->sendResponse(EnquiryResource::make($enquiry), 'Enquiry updated Successfully', 200);
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
            EnquiryRequirement::where('enquiry_id', $id)->delete();
            Enquiry::findOrFail($id)->delete();
            return $this->sendResponse([], 'Enquiry Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
