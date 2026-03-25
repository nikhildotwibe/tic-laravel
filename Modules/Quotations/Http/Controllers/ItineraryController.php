<?php

namespace Modules\Quotations\Http\Controllers;

use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator as ValidationValidator;
use Modules\Quotations\Entities\Itinerary;
use Modules\Quotations\Entities\ItineraryEntry;
use Modules\Quotations\Transformers\ItineraryResource;
use Modules\Settings\Entities\Activity;
use Modules\Settings\Entities\ActivityEstimation;
use Modules\Settings\Entities\Enquiry;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\Transfer;
use \Mpdf\Mpdf as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ItineraryController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            Validator::make($request->all(), [
                'enquiry_id' => 'nullable|exists:enquiries,id,deleted_at,NULL',
            ]);

            $itinerary = Itinerary::query();
            if (request()->has('enquiry_id')) {
                $itinerary = $itinerary->where('enquiry_id', $request->enquiry_id);
            }

            if (request()->has('package_name')) {
                $itinerary = $itinerary->where('package_name', 'LIKE','%'.$request->package_name.'%');
            }
            
            $itinerary = $itinerary->latest()->get();
            return $this->sendResponse(ItineraryResource::collection($itinerary), 'All Itineraries Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function requestValidator($requestData, string|null $id = null): ValidationValidator
    {
        $rules =
            [
                'package_name' => 'required',
                'enquiry_id' => 'required|exists:enquiries,id,deleted_at,NULL',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'adult_count' => 'required|gte:0',
                'child_count' => 'required|gte:0',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'valid_until' => 'required|date_format:Y-m-d',
                'price_mode' => 'required',

                'entries' => 'required|array',
                'entries.*.entry_type' => 'required|in:HOTEL,ACTIVITY,TRANSFER',
                'entries.*.subject_id' => 'required',
                'entries.*.date' => 'required|date_format:Y-m-d',

                // HOTEL Specific
                'entries.*.room_id' => 'required_if:entries.*.entry_type,HOTEL|exists:rooms,id,deleted_at,NULL',
                'entries.*.no_of_person' => 'required_if:entries.*.entry_type,HOTEL,ACTIVITY|gte:0',
                'entries.*.single_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.double_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.triple_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.extra_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.child_w_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.child_n_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',

                // ACTIVITY Specific
                // 'entries.*.description' => 'required_if:entries.*.entry_type,ACTIVITY',

                // TRANSFER specific
                'entries.*.transfer_type' => 'required_if:entries.*.entry_type,TRANSFER|in:PRIVATE,SIC',
                'entries.*.cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,PRIVATE|gte:0',
                'entries.*.adult_cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,SIC|gte:0',
                'entries.*.child_cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,SIC|gte:0',

                'entries.*.start_date' => 'required|date_format:Y-m-d',
                'entries.*.start_time' => 'required|date_format:H:i:s',
                'entries.*.end_date' => 'required|date_format:Y-m-d',
                'entries.*.end_time' => 'required|date_format:H:i:s',
            ];

        return Validator::make($requestData, $rules)->setAttributeNames(
            [

                'entries.*.entry_type' => 'Entry Type',
                'entries.*.date' => 'Date',
                'entries.*.subject_id' => 'Subject ID',

                // HOTEL Specific
                'entries.*.room_id' => 'Room ID',
                'entries.*.no_of_person' => 'No of Person',
                'entries.*.single_count' => 'Single Count',
                'entries.*.double_count' => 'Double Count',
                'entries.*.triple_count' => 'Triple Count',
                'entries.*.extra_count' => 'Extra Count',
                'entries.*.child_w_count' => 'Child W Count',
                'entries.*.child_n_count' => 'Child N Count',

                // ACTIVITY Specific
                'entries.*.description' => 'Description',

                // TRANSFER specific
                'entries.*.transfer_type' => 'Transfer Type',
                'entries.*.cost' => 'Cost',
                'entries.*.adult_cost' => 'Adult Cost',
                'entries.*.child_cost' => 'Child Cost',

                'entries.*.start_date' => 'Start Date',
                'entries.*.start_time' => 'Start Time',
                'entries.*.end_date' => 'End Date',
                'entries.*.end_time' => 'End Time',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all())->validate();
            $itinerary = $this->process($request->all());
            DB::commit();
            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary created Successfully', 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->HandleException($exception);
        }
    }

    public function process($requestData, string|null $id = null)
    {

        $entriesData = $requestData['entries'];
        unset($requestData['entries']);

        $itinerary = Itinerary::updateOrCreate(['id' => $id], $requestData);

        $savedItems = [];

        foreach ($entriesData as $key => $entry) {
            $entryData = [];

            $entryData['date'] = $entry['date'];
            $entryData['itinerary_id'] = $itinerary->id;
            $entryData['entry_type'] = $entry['entry_type'];

            if ($entry['entry_type'] == 'HOTEL') {

                // $entryData['no_of_person'] = $entry['no_of_person'];
                $entryData['option'] = $entry['option'] ?? 'option 1';
                $entryData['room_id'] = $entry['room_id'];
                $entryData['single_count'] = $entry['single_count'];
                $entryData['double_count'] = $entry['double_count'];
                $entryData['triple_count'] = $entry['triple_count'];
                $entryData['extra_count'] = $entry['extra_count'];
                $entryData['child_w_count'] = $entry['child_w_count'];
                $entryData['child_n_count'] = $entry['child_n_count'];

                // set pricing 

                $room = Room::findOrFail($entry['room_id']);
                $singlePrice = $entry['single_count'] * $room->single_bed_amount;
                $doublePrice = $entry['double_count'] * $room->double_bed_amount;
                $triplePrice = $entry['triple_count'] * $room->triple_bed_amount;
                $extraPrice = $entry['extra_count'] * $room->extra_bed_amount;
                $childWPrice = $entry['child_w_count'] * $room->child_w_bed_amount;
                $childNPrice = $entry['child_n_count'] * $room->child_n_bed_amount;

                $entryData['amount'] = $singlePrice + $doublePrice + $triplePrice + $extraPrice + $childWPrice + $childNPrice;
            } elseif ($entry['entry_type'] == 'ACTIVITY') {

                $entryData['description'] = $entry['description'];


                // set pricing

                $entryData['amount'] = 0;
                $activityStartDate = $entry['start_date'];
                $activityEndDate = $entry['end_date'];

                $activityEstimation = ActivityEstimation::where('activity_id', $entry['subject_id'])->whereDate('from_date', '<=', $activityStartDate)->whereDate('to_date', '>=', $activityEndDate)->first();

                if ($activityEstimation) {

                    $enquiry = Enquiry::findOrFail($requestData['enquiry_id']);

                    $adultActivityAmount = $activityEstimation->adult_cost * $enquiry->adult_count;
                    $childActivityAmount = $activityEstimation->child_cost * $enquiry->child_count;

                    $entryData['amount'] = $adultActivityAmount + $childActivityAmount;
                }
            } elseif ($entry['entry_type'] == 'TRANSFER') {

                $entryData['transfer_type'] = $entry['transfer_type'];
                if ($entry['transfer_type'] == 'PRIVATE') {
                    $entryData['cost'] = $entry['cost'];
                    $entryData['amount'] = $entry['cost'];
                } elseif ($entry['transfer_type'] == 'SIC') {
                    $entryData['adult_cost'] = $entry['adult_cost'];
                    $entryData['child_cost'] = $entry['child_cost'];

                    $entryData['amount'] = $entry['adult_cost'] + $entry['child_cost'];
                }
            }

            $entryData['no_of_person'] = $entry['no_of_person'];

            $entryData['start_date'] = $entry['start_date'];
            $entryData['start_time'] = $entry['start_time'];
            $entryData['end_date'] = $entry['end_date'];
            $entryData['end_time'] = $entry['end_time'];

            $entryData['subject_id'] = $entry['subject_id'];
            $entryData['sub_destination_id'] = $entry['sub_destination_id'];


            $itineraryEntry = ItineraryEntry::updateOrCreate(['id' => $entry['id'] ?? null], $entryData);

            $savedItems[] = $itineraryEntry;
        }

        ItineraryEntry::where('itinerary_id', $id)->whereNotIn('id', collect($savedItems)->pluck('id'))->delete();

        return $itinerary;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);
            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary fetched Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all())->validate();
            $itinerary = $this->process($request->all(), $id);
            DB::commit();
            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary updated Successfully', 200);
        } catch (Exception $exception) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {

            ItineraryEntry::where('itinerary_id', $id)->delete();
            Itinerary::where('id', $id)->delete();

            DB::commit();
            return $this->sendResponse([], 'Itinerary deleted Successfully', 200);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->HandleException($exception);
        }
    }

    /**
     * get pricing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    // public function pricing($id)
    // {
    //     try {
    //         $itineraryEntries = ItineraryEntry::where('itinerary_id', $id)->get();
    //         $itinerary = Itinerary::findOrFail($id);
    //         $enquiry = $itinerary->enquiry;

    //         $itineraryEntryPricing = [];
    //         foreach ($itineraryEntries as $key => $itineraryEntry) {
    //             $netAmount = 0;
    //             if ($itineraryEntry->entry_type == 'HOTEL') {
    //                 $room = Room::findOrFail($itineraryEntry->room_id);
    //                 $singlePrice = $itineraryEntry->single_count * $room->single_bed_amount;
    //                 $doublePrice = $itineraryEntry->double_count * $room->double_bed_amount;
    //                 $triplePrice = $itineraryEntry->triple_count * $room->triple_bed_amount;
    //                 $extraPrice = $itineraryEntry->extra_count * $room->extra_bed_amount;
    //                 $childWPrice = $itineraryEntry->child_w_count * $room->child_w_bed_amount;
    //                 $childNPrice = $itineraryEntry->child_n_count * $room->child_n_bed_amount;

    //                 $netAmount = $singlePrice + $doublePrice + $triplePrice + $extraPrice + $childWPrice + $childNPrice;
    //             } elseif ($itineraryEntry->entry_type == 'ACTIVITY') {

    //                 $activityStartDate = $itineraryEntry->start_date;
    //                 $activityEndDate = $itineraryEntry->end_date;

    //                 $activityEstimation = ActivityEstimation::where('activity_id', $itineraryEntry->subject_id)->whereDate('from_date', '>=', $activityStartDate)->whereDate('to_date', '<=', $activityEndDate)->first();

    //                 if ($activityEstimation) {
    //                     $adultActivityAmount = $activityEstimation->adult_cost * $enquiry->adult_count;
    //                     $childActivityAmount = $activityEstimation->child_cost * $enquiry->child_count;

    //                     $netAmount = $adultActivityAmount + $childActivityAmount;
    //                 }
    //             } elseif ($itineraryEntry->entry_type == 'TRANSFER') {
    //                 if ($itineraryEntry->transfer_type == 'PRIVATE') {
    //                     $netAmount = $itineraryEntry->cost;
    //                 } elseif ($itineraryEntry->transfer_type == 'SIC') {
    //                     $netAmount = $itineraryEntry->adult_cost + $itineraryEntry->child_cost;
    //                 }
    //             }

    //             $itineraryEntryPricing[] = [
    //                 'entry' => $itineraryEntry,
    //                 'net_amount' => $netAmount,
    //             ];
    //         }

    //         return $this->sendResponse($itineraryEntryPricing, 'Itinerary fetched Successfully', 200);
    //     } catch (Exception $exception) {
    //         return $this->HandleException($exception);
    //     }
    // }


    /**
     * Set Pricing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function setPricing(Request $request, $id)
    {
        try {

            $itinerary = Itinerary::findOrFail($id);

            Validator::make($request->all(), [
                'entries' => 'required|array|min:1',
                'entries.*.id' => 'required|exists:itinerary_entries,id,deleted_at,NULL',
                'entries.*.amount' => 'required|gte:0',
                'entries.*.markup' => 'required|min:0|max:100',
                'extra_markup_percentage' => 'required|min:0|max:100',
                'extra_markup_amount' => 'required|gte:0',
                'cgst_percentage' => 'required|min:0|max:100',
                'sgst_percentage' => 'required|min:0|max:100',
                'igst_percentage' => 'required|min:0|max:100',
                'tcs_percentage' => 'required|min:0|max:100',
                'discount_amount' => 'required|gte:0',
                'currency' => 'required',
                'description' => 'required',
                'price_mode' => 'required|in:PER_PERSON,TOTAL_PRICE',
            ])->setAttributeNames([
                'entries.*.id' => 'ID',
                'entries.*.amount' => 'Amount',
                'entries.*.markup' => 'Mark Up',
            ])->validate();

            foreach ($request->entries as $key => $entryData) {
                $entry = ItineraryEntry::findOrFail($entryData["id"]);
                $entry->amount = $entryData["amount"];
                $entry->markup = $entryData["markup"];
                $entry->save();
            }

            $itinerary->extra_markup_amount = $request->extra_markup_amount;
            $itinerary->extra_markup_percentage = $request->extra_markup_percentage;
            $itinerary->cgst_percentage = $request->cgst_percentage;
            $itinerary->sgst_percentage = $request->sgst_percentage;
            $itinerary->igst_percentage = $request->igst_percentage;
            $itinerary->tcs_percentage = $request->tcs_percentage;
            $itinerary->discount_amount = $request->discount_amount;
            $itinerary->currency = $request->currency;
            $itinerary->description = $request->description;
            $itinerary->price_mode = $request->price_mode;
            $itinerary->total_amount = $request->total_amount;
            $itinerary->grand_total = $request->grand_total;
            $itinerary->converted_total = $request->converted_total;
            $itinerary->exchange_rate = $request->exchange_rate;
            $itinerary->save();

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary Prices Successfully fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function print(String $id)
    {
        $itinerary = Itinerary::findOrFail($id);

        // Setup a filename 
        $documentFileName = "fun.pdf";

        // Create the mPDF document
        $document = new PDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '10',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);

        // Set some header informations for output
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $documentFileName . '"'
        ];



        // Write some simple Content
        // $document->WriteHTML('<h1 style="color:blue">TheCodingJack</h1>');
        // $document->WriteHTML('<p>Write something, just for fun!</p>');

        $html = View::make(
            'itinerary.print.template1',
            [
                'itinerary' => $itinerary,

            ]
        )->render();
        $document->WriteHTML($html);

        // Save the PDF to public storage
        Storage::disk('public')->put($documentFileName, $document->Output($documentFileName, \Mpdf\Output\Destination::STRING_RETURN));

        // Set headers for the response
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $documentFileName . '"'
        ];

        // Return the file as a response
        return response()->file(storage_path('app/public/' . $documentFileName), $headers);

    }
}
