<?php

namespace Modules\Quotations\Http\Controllers;

use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Validator as ValidationValidator;
use Modules\Quotations\Emails\ShareItineraryMail;
use Modules\Quotations\Entities\Itinerary;
use Modules\Quotations\Entities\ItineraryEntry;
use Modules\Quotations\Entities\PricingSnapshot;
use Modules\Quotations\Transformers\ItineraryResource;
use Modules\Settings\Entities\Activity;
use Modules\Settings\Entities\ActivityEstimation;
use Modules\Settings\Entities\Enquiry;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\Transfer;
use Mpdf\Mpdf as PDF;

class ItineraryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
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
                $itinerary = $itinerary->where('package_name', 'LIKE', '%'.$request->package_name.'%');
            }

            $itinerary = $itinerary->with([
                'enquiry.agent',
                'enquiry.customer',
                'enquiry.destination',
                'enquiry.sub_destination',
                'enquiry.sub_destinations',
                'enquiry.lead_source',
                'enquiry.requirements',
                'enquiry.priority',
                'enquiry.assigned_to_user',
                'destination',
                'creator',
                'updater',
                'currency_obj',
                'entries.room',
                'entries.sub_destination',
                'entries.hotel',
                'entries.activity.estimations',
                'entries.transfer',
            ])->latest()->get();

            return $this->sendResponse(ItineraryResource::collection($itinerary), 'All Itineraries Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function requestValidator($requestData, string $id = null): ValidationValidator
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
                'entries.*.adult_count' => 'nullable|gte:0',
                'entries.*.child_count' => 'nullable|gte:0',
                'entries.*.single_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.double_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.triple_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.quad_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.two_bedroom_count' => 'nullable|gte:0',
                'entries.*.three_bedroom_count' => 'nullable|gte:0',
                'entries.*.four_bedroom_count' => 'nullable|gte:0',
                'entries.*.extra_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.child_w_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.child_n_count' => 'required_if:entries.*.entry_type,HOTEL|gte:0',
                'entries.*.room_rows' => 'nullable|string',

                // ACTIVITY Specific
                // 'entries.*.description' => 'required_if:entries.*.entry_type,ACTIVITY',

                // TRANSFER specific
                'entries.*.transfer_type' => 'required_if:entries.*.entry_type,TRANSFER|in:PRIVATE,SIC',
                'entries.*.cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,PRIVATE|gte:0',
                'entries.*.adult_cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,SIC|gte:0',
                'entries.*.child_cost' => 'required_if:entries.*.entry_type,TRANSFER|required_if:entries.*.transfer_type,SIC|gte:0',
                'entries.*.vehicle_count' => 'nullable|integer',
                'entries.*.vehicle_type' => 'nullable|string',

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
                'entries.*.quad_count' => 'Quad Count',
                'entries.*.two_bedroom_count' => 'Two Bedroom Count',
                'entries.*.three_bedroom_count' => 'Three Bedroom Count',
                'entries.*.four_bedroom_count' => 'Four Bedroom Count',
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
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all())->validate();
            $itinerary = $this->process($request->all());

            // Initialise version tracking for new itineraries
            if (! $itinerary->parent_itinerary_id) {
                $itinerary->parent_itinerary_id = $itinerary->id;
                $itinerary->version = 1;
                $itinerary->is_current = true;
                $itinerary->save();
            }

            DB::commit();

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary created Successfully', 201);
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->HandleException($exception);
        }
    }

    public function process($requestData, string $id = null)
    {

        if (auth()->check()) {
            if (! $id) {
                $requestData['created_by'] = auth()->id();
            }
            $requestData['updated_by'] = auth()->id();
        }

        $entriesData = $requestData['entries'];
        unset($requestData['entries']);

        if (!\Illuminate\Support\Facades\Schema::hasColumn('itinerary_entries', 'sort_order')) {
            \Illuminate\Support\Facades\Schema::table('itinerary_entries', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->integer('sort_order')->default(0);
            });
        }

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
                $entryData['quad_count'] = $entry['quad_count'];
                $entryData['two_bedroom_count'] = $entry['two_bedroom_count'] ?? 0;
                $entryData['three_bedroom_count'] = $entry['three_bedroom_count'] ?? 0;
                $entryData['four_bedroom_count'] = $entry['four_bedroom_count'] ?? 0;
                $entryData['extra_count'] = $entry['extra_count'];
                $entryData['child_w_count'] = $entry['child_w_count'];
                $entryData['child_n_count'] = $entry['child_n_count'];
                $entryData['room_rows'] = $entry['room_rows'] ?? null;
                $entryData['no_of_person'] = $entry['no_of_person'];
                $entryData['description'] = $entry['description'] ?? null;

                // set pricing
                $room = Room::findOrFail($entry['room_id']);
                $singlePrice = $entry['single_count'] * $room->single_bed_amount;
                $doublePrice = $entry['double_count'] * $room->double_bed_amount;
                $triplePrice = $entry['triple_count'] * $room->triple_bed_amount;
                $quadPrice = $entry['quad_count'] * $room->quad_bed_amount;
                $twoBedroomPrice = ($entry['two_bedroom_count'] ?? 0) * ($room->two_bedroom_amount ?? 0);
                $threeBedroomPrice = ($entry['three_bedroom_count'] ?? 0) * ($room->three_bedroom_amount ?? 0);
                $fourBedroomPrice = ($entry['four_bedroom_count'] ?? 0) * ($room->four_bedroom_amount ?? 0);
                $extraPrice = $entry['extra_count'] * $room->extra_bed_amount;
                $childWPrice = $entry['child_w_count'] * $room->child_w_bed_amount;
                $childNPrice = $entry['child_n_count'] * $room->child_n_bed_amount;

                $entryData['amount'] = $singlePrice + $doublePrice + $triplePrice + $quadPrice + $twoBedroomPrice + $threeBedroomPrice + $fourBedroomPrice + $extraPrice + $childWPrice + $childNPrice;
            } elseif ($entry['entry_type'] == 'ACTIVITY') {

                $entryData['description'] = $entry['description'];
                $entryData['adult_count'] = $entry['adult_count'] ?? 0;
                $entryData['child_count'] = $entry['child_count'] ?? 0;
                $entryData['no_of_person'] = $entryData['adult_count'] + $entryData['child_count'];

                // set pricing
                $entryData['amount'] = 0;
                $activityStartDate = $entry['start_date'];
                $activityEndDate = $entry['end_date'];

                $activityEstimation = ActivityEstimation::where('activity_id', $entry['subject_id'])->whereDate('from_date', '<=', $activityStartDate)->whereDate('to_date', '>=', $activityEndDate)->first();

                if ($activityEstimation) {
                    $adultCount = (isset($entry['adult_count']) && $entry['adult_count'] !== null) ? $entry['adult_count'] : $requestData['adult_count'];
                    $childCount = (isset($entry['child_count']) && $entry['child_count'] !== null) ? $entry['child_count'] : $requestData['child_count'];

                    $entryData['adult_count'] = $adultCount;
                    $entryData['child_count'] = $childCount;
                    $entryData['no_of_person'] = $adultCount + $childCount;

                    $entryData['adult_cost'] = $activityEstimation->adult_cost * $adultCount;
                    $entryData['child_cost'] = $activityEstimation->child_cost * $childCount;
                    $entryData['amount'] = $entryData['adult_cost'] + $entryData['child_cost'];
                }
            } elseif ($entry['entry_type'] == 'TRANSFER') {

                $entryData['transfer_type'] = $entry['transfer_type'];
                $entryData['adult_count'] = $entry['adult_count'] ?? $requestData['adult_count'];
                $entryData['child_count'] = $entry['child_count'] ?? $requestData['child_count'];
                $entryData['no_of_person'] = $entryData['adult_count'] + $entryData['child_count'];
                $entryData['vehicle_count'] = $entry['vehicle_count'] ?? 1;
                $entryData['vehicle_type'] = $entry['vehicle_type'] ?? null;

                if ($entry['transfer_type'] == 'PRIVATE') {
                    $entryData['cost'] = $entry['cost'];
                    $entryData['amount'] = $entry['cost'];
                } elseif ($entry['transfer_type'] == 'SIC') {
                    $entryData['adult_cost'] = $entry['adult_cost'];
                    $entryData['child_cost'] = $entry['child_cost'];

                    $entryData['amount'] = $entry['adult_cost'] + $entry['child_cost'];
                }
            }

            $entryData['no_of_person'] = $entryData['no_of_person'] ?? $entry['no_of_person'];

            $entryData['start_date'] = $entry['start_date'];
            $entryData['start_time'] = $entry['start_time'];
            $entryData['end_date'] = $entry['end_date'];
            $entryData['end_time'] = $entry['end_time'];

            $entryData['subject_id'] = $entry['subject_id'];
            $entryData['sub_destination_id'] = $entry['sub_destination_id'];
            $entryData['sort_order'] = $entry['seq'] ?? 0;

            $itineraryEntry = ItineraryEntry::updateOrCreate(['id' => $entry['id'] ?? null], $entryData);

            $savedItems[] = $itineraryEntry;
        }

        ItineraryEntry::where('itinerary_id', $id)->whereNotIn('id', collect($savedItems)->pluck('id'))->delete();

        return $itinerary;
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $itinerary = Itinerary::with([
                'enquiry.agent',
                'enquiry.customer',
                'enquiry.destination',
                'enquiry.sub_destination',
                'enquiry.sub_destinations',
                'enquiry.lead_source',
                'enquiry.requirements',
                'enquiry.priority',
                'enquiry.assigned_to_user',
                'destination',
                'creator',
                'updater',
                'currency_obj',
                'entries.room',
                'entries.sub_destination',
                'entries.hotel',
                'entries.activity.estimations',
                'entries.transfer',
            ])->findOrFail($id);

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary fetched Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all())->validate();

            $currentItinerary = Itinerary::with('entries')->findOrFail($id);
            
            // Only snapshot if something actually changed
            if ($this->hasItineraryChanges($currentItinerary, $request->all())) {
                $currentItinerary->createVersionSnapshot();
            }

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
     *
     * @param  int  $id
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
     *
     * @param  int  $id
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
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function setPricing(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $itinerary = Itinerary::with('entries')->findOrFail($id);

            // Only snapshot if pricing actually changed
            if ($this->hasPricingChanges($itinerary, $request->all())) {
                $itinerary->createVersionSnapshot();
            }

            Validator::make($request->all(), [
                'entries' => 'required|array|min:1',
                'entries.*.id' => 'required|exists:itinerary_entries,id,deleted_at,NULL',
                'entries.*.amount' => 'required|gte:0',
                'entries.*.markup' => 'required|min:0|max:100',
                // base_amount / base_markup are optional — older clients won't send them
                'entries.*.base_amount' => 'nullable|numeric|gte:0',
                'entries.*.base_markup' => 'nullable|numeric|min:0',
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
                $entry = ItineraryEntry::findOrFail($entryData['id']);
                $entry->amount = $entryData['amount'];
                $entry->markup = $entryData['markup'];
                // Persist canonical totals so the frontend never needs to re-derive them on reload.
                // base_amount is always the TOTAL cost regardless of PER/TOTAL price mode.
                $entry->base_amount = isset($entryData['base_amount']) ? $entryData['base_amount'] : $entryData['amount'];
                $entry->base_markup = isset($entryData['base_markup']) ? $entryData['base_markup'] : $entryData['markup'];
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
            if ($request->has('quoted_options')) {
                $itinerary->quoted_options = $request->quoted_options;
            }
            $itinerary->save();

            // Auto-create pricing snapshot
            $snapshotEntries = [];
            foreach ($request->entries as $entryData) {
                $snapshotEntries[] = [
                    'id' => $entryData['id'],
                    'amount' => $entryData['amount'],
                    'markup' => $entryData['markup'],
                ];
            }
            PricingSnapshot::create([
                'itinerary_id' => $itinerary->id,
                'snapshot_data' => json_encode([
                    'entries' => $snapshotEntries,
                    'itinerary' => [
                        'extra_markup_percentage' => $itinerary->extra_markup_percentage,
                        'extra_markup_amount' => $itinerary->extra_markup_amount,
                        'cgst_percentage' => $itinerary->cgst_percentage,
                        'sgst_percentage' => $itinerary->sgst_percentage,
                        'igst_percentage' => $itinerary->igst_percentage,
                        'tcs_percentage' => $itinerary->tcs_percentage,
                        'discount_amount' => $itinerary->discount_amount,
                        'currency' => $itinerary->currency,
                        'price_mode' => $itinerary->price_mode,
                        'total_amount' => $itinerary->total_amount,
                        'grand_total' => $itinerary->grand_total,
                        'converted_total' => $itinerary->converted_total,
                        'exchange_rate' => $itinerary->exchange_rate,
                        'description' => $itinerary->description,
                        'quoted_options' => $itinerary->quoted_options,
                    ],
                ]),
                'grand_total' => $itinerary->grand_total ?? 0,
                'currency' => $itinerary->currency,
                'created_by' => auth()->check() ? auth()->user()->id : null,
            ]);

            DB::commit();

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary Prices Successfully fetched', 200);
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->HandleException($exception);
        }
    }

    /**
     * Get pricing history for an itinerary.
     */
    public function pricingHistory($id)
    {
        try {
            $snapshots = PricingSnapshot::where('itinerary_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($snapshot) {
                    return [
                        'id' => $snapshot->id,
                        'grand_total' => $snapshot->grand_total,
                        'currency' => $snapshot->currency,
                        'notes' => $snapshot->notes,
                        'created_at' => $snapshot->created_at,
                        'created_by' => $snapshot->creator ? trim($snapshot->creator->first_name.' '.$snapshot->creator->last_name) : null,
                        'snapshot_data' => $snapshot->snapshot_data,
                    ];
                });

            return $this->sendResponse($snapshots, 'Pricing History Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Restore pricing from a snapshot.
     */
    public function restorePricing(Request $request, $id, $snapshotId)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);
            $snapshot = PricingSnapshot::where('itinerary_id', $id)->findOrFail($snapshotId);

            $data = is_string($snapshot->snapshot_data)
                ? json_decode($snapshot->snapshot_data, true)
                : $snapshot->snapshot_data;

            if (! $data || ! isset($data['entries']) || ! isset($data['itinerary'])) {
                return $this->sendError('Invalid snapshot data', [], 422);
            }

            // Restore entry amounts & markup
            foreach ($data['entries'] as $entryData) {
                $entry = ItineraryEntry::find($entryData['id']);
                $isSharing = (
                    $entry->double_count > 0 ||
                    $entry->triple_count > 0 ||
                    $entry->quad_count > 0
                );
                if ($entry) {
                    $entry->amount = $entryData['amount'];
                    $entry->markup = $entryData['markup'];
                    $entry->save();
                }
            }

            // Restore itinerary-level pricing fields
            $itineraryData = $data['itinerary'];
            $itinerary->extra_markup_percentage = $itineraryData['extra_markup_percentage'] ?? 0;
            $itinerary->extra_markup_amount = $itineraryData['extra_markup_amount'] ?? 0;
            $itinerary->cgst_percentage = $itineraryData['cgst_percentage'] ?? 0;
            $itinerary->sgst_percentage = $itineraryData['sgst_percentage'] ?? 0;
            $itinerary->igst_percentage = $itineraryData['igst_percentage'] ?? 0;
            $itinerary->tcs_percentage = $itineraryData['tcs_percentage'] ?? 0;
            $itinerary->discount_amount = $itineraryData['discount_amount'] ?? 0;
            $itinerary->currency = $itineraryData['currency'] ?? null;
            $itinerary->price_mode = $itineraryData['price_mode'] ?? 'TOTAL_PRICE';
            $itinerary->total_amount = $itineraryData['total_amount'] ?? 0;
            $itinerary->grand_total = $itineraryData['grand_total'] ?? 0;
            $itinerary->converted_total = $itineraryData['converted_total'] ?? 0;
            $itinerary->exchange_rate = $itineraryData['exchange_rate'] ?? 1;
            $itinerary->description = $itineraryData['description'] ?? '';
            $itinerary->quoted_options = $itineraryData['quoted_options'] ?? null;
            $itinerary->save();

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Pricing Restored Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Switch the active (current) version for a quotation group.
     * Sets the selected version as current and all siblings as previous.
     */
    public function setCurrent($id)
    {
        DB::beginTransaction();
        try {
            $selected = Itinerary::findOrFail($id);
            $parentId = $selected->parent_itinerary_id ?? $selected->id;

            // Set all siblings (including current active) to not-current
            Itinerary::where('parent_itinerary_id', $parentId)
                ->update(['is_current' => false]);

            // Promote the selected version
            $selected->is_current = true;
            $selected->save();

            DB::commit();

            return $this->sendResponse(
                ItineraryResource::make($selected->fresh()),
                'Version set as current successfully',
                200
            );
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->HandleException($exception);
        }
    }

    public function print(string $id)
    {
        $itinerary = Itinerary::findOrFail($id);

        // Generate a unique filename to prevent browser caching
        $documentFileName = 'itinerary_'.$itinerary->id.'_'.time().'.pdf';

        // Create the mPDF document
        $document = new PDF([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '10',
            'margin_bottom' => '20',
            'margin_footer' => '2',
        ]);

        $html = View::make(
            'itinerary.print.template1',
            [
                'itinerary' => $itinerary,
            ]
        )->render();
        $document->WriteHTML($html);

        // Send the PDF as a response with cache-busting headers
        return response($document->Output($documentFileName, \Mpdf\Output\Destination::STRING_RETURN))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$documentFileName.'"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Preview HTML response for Email Sharing
     *
     * @return JsonResponse
     */
    public function previewHtml(Request $request, string $id)
    {
        try {
            $itinerary = Itinerary::findOrFail($id);

            $options = [
                'priceBreakup' => $request->query('priceBreakup', 'true') === 'true',
                'hideTotalPrice' => $request->query('hideTotalPrice', 'false') === 'true',
                'itinerary' => $request->query('itinerary', 'true') === 'true',
                'terms' => $request->query('terms', 'false') === 'true',
            ];

            $html = View::make(
                'itinerary.print.template1',
                [
                    'itinerary' => $itinerary,
                    'options'   => $options,
                ]
            )->render();

            return $this->sendResponse(['html' => $html], 'HTML preview fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    private function getOrdinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number.'th';
        } else {
            return $number.$ends[$number % 10];
        }
    }

    /**
     * Preview WhatsApp response
     *
     * @return JsonResponse
     */
    public function previewWhatsapp(Request $request, string $id)
    {
        try {
            $itinerary = Itinerary::with(['enquiry', 'destination', 'entries'])->findOrFail($id);
            $enquiry = $itinerary->enquiry;

            $priceBreakup = $request->query('priceBreakup', 'true') === 'true';
            $hideTotalPrice = $request->query('hideTotalPrice', 'false') === 'true';
            $includeItinerary = $request->query('itinerary', 'true') === 'true';

            $startDate = Carbon::parse($itinerary->start_date);
            $endDate = Carbon::parse($itinerary->end_date);
            $nightsCount = $startDate->diffInDays($endDate);
            $daysCount = $nightsCount + 1;

            $text = 'Hi '.($enquiry->customer_name ?? 'Customer').",\n\n";
            $text .= "Greetings from *TIC Tours.*\n\n";
            $text .= "Thank you for your query with us. As per your requirements, following are the package details.\n\n";

            $text .= '*Trip ID '.($enquiry->ref_no ?? $itinerary->seq ?? $itinerary->id)."*\n";
            $text .= "----------\n";
            $text .= '*'.($itinerary->package_name ?? 'Package')."*\n";
            $text .= '• *'.$startDate->format('d M Y')."* _for_ *{$nightsCount} Nights, {$daysCount} Days*\n";
            $text .= '• *'.$itinerary->adult_count.' Adults*'.($itinerary->child_count > 0 ? ' and '.$itinerary->child_count.' Child' : '')."\n\n";

            if (! $hideTotalPrice) {
                // ── Resolve currency and grand total ──
                // Step 1: Try to get currency from quoted_options JSON (most accurate — matches UI)
                $currencyCode = 'USD';
                $currencySymbol = '$';
                $finalGrandTotal = floatval($itinerary->grand_total ?? 0);
                $quotedOptions = null;
                $firstOption = null;

                if ($itinerary->quoted_options) {
                    $quotedOptions = is_string($itinerary->quoted_options) ? json_decode($itinerary->quoted_options, true) : $itinerary->quoted_options;
                    if (is_array($quotedOptions) && ! empty($quotedOptions)) {
                        $firstOption = $quotedOptions[0];
                        // Currency from quoted_options (this is the converted/display currency from UI)
                        $currencyCode = $firstOption['currencyCode'] ?? $currencyCode;
                        $currencySymbol = $firstOption['currencySymbol'] ?? $currencySymbol;
                    }
                }

                // Step 2: If quoted_options didn't provide currency, resolve from DB
                if ($currencyCode === 'USD' && $itinerary->currency) {
                    $currencyModel = \Modules\Settings\Entities\Currency::find($itinerary->currency);
                    if ($currencyModel) {
                        $currencyCode = $currencyModel->code ?? $currencyCode;
                        $currencySymbol = $currencyModel->symbol ?? $currencySymbol;
                    }
                }

                // Step 3: Grand total — use the itinerary's actual grand_total (includes taxes/markup/discount)
                // Apply exchange rate conversion if a converted currency is being displayed
                $exchangeRate = floatval($itinerary->exchange_rate ?? 1);
                if ($exchangeRate > 0 && $exchangeRate != 1 && $itinerary->converted_total) {
                    // Converted currency is active — use the pre-calculated converted total
                    $finalGrandTotal = floatval($itinerary->converted_total);
                }
                // else: $finalGrandTotal already set to $itinerary->grand_total (base currency)

                $isPERMode = ($itinerary->price_mode === 'PER_PERSON' || $itinerary->price_mode === 'PER_TRAVELLER');

                // ── Price breakdown rows ──
                if ($priceBreakup && $firstOption) {
                    $text .= "*Price ({$currencyCode}):*\n";

                    $rows = $firstOption['rows'] ?? [];
                    foreach ($rows as $row) {
                        $label = $row['label'] ?? 'Person';
                        $count = intval($row['count'] ?? 0);
                        $perPerson = floatval($row['perPerson'] ?? 0);
                        $rowTotal = floatval($row['total'] ?? 0);

                        // Ensure consistent perPerson/rowTotal regardless of how data was stored
                        if ($perPerson > 0 && $rowTotal <= 0) {
                            $rowTotal = $perPerson * $count;
                        } elseif ($rowTotal > 0 && $perPerson <= 0 && $count > 0) {
                            $perPerson = $rowTotal / $count;
                        }

                        $isSharing = (stripos($label, 'double') !== false || stripos($label, 'triple') !== false || stripos($label, 'quad') !== false);

                        if ($isSharing && $isPERMode) {
                            // Show per-person rate for sharing types
                            $countSuffix = $count > 1 ? " x {$count}" : '';
                            $text .= "• *{$label}*\t\t{$currencySymbol} ".number_format(floor($perPerson), 0).$countSuffix."\n";
                        } else {
                            // Show total for this person type
                            $countSuffix = $count > 1 ? " x {$count}" : '';
                            $text .= "• *{$label}*\t\t- {$currencySymbol} ".number_format(floor($rowTotal), 0).$countSuffix."\n";
                        }
                    }
                } else {
                    $text .= "*Price ({$currencyCode}):*\n";
                }

                $total = number_format(floor($finalGrandTotal), 0);
                $text .= "*Total: {$currencySymbol} {$total} /-* _(exc. Vat)_\n\n";
            }

            if ($includeItinerary) {
                // Hotels Section
                $entriesByOption = $itinerary->entries()->where('entry_type', 'HOTEL')->orderBy('date')->get();
                if ($entriesByOption->count() > 0) {
                    $text .= "🏨  *_Hotels_*\n";
                    $text .= "-----------\n";

                    // Simple grouping by hotel name
                    $groupedHotels = [];
                    foreach ($entriesByOption as $index => $entry) {
                        $hotel = \Modules\Settings\Entities\Hotel::find($entry->subject_id);
                        $room = \Modules\Settings\Entities\Room::find($entry->room_id);
                        $hotelName = $hotel ? $hotel->name : 'Hotel';
                        $location = $entry->sub_destination_id ? (\Modules\Settings\Entities\SubDestination::find($entry->sub_destination_id)->name ?? 'Destination') : 'Destination';

                        $nightsKey = $hotelName.'-'.$location;
                        if (! isset($groupedHotels[$nightsKey])) {
                            $groupedHotels[$nightsKey] = [
                                'name' => $hotelName,
                                'location' => $location,
                                'nights' => [],
                                'checkIn' => Carbon::parse($entry->date),
                                'checkOut' => Carbon::parse($entry->date)->addDay(),
                                'room' => $room ? $room->name : 'Room',
                                'meal' => 'Bed and Breakfast', // Default or fetch if available
                                'pax' => $itinerary->adult_count,
                            ];
                        }
                        $groupedHotels[$nightsKey]['nights'][] = $index + 1;
                        $groupedHotels[$nightsKey]['checkOut'] = Carbon::parse($entry->date)->addDay();
                    }

                    foreach ($groupedHotels as $h) {
                        $nightOrdinals = array_map([$this, 'getOrdinal'], $h['nights']);
                        $nightStr = implode(', ', $nightOrdinals).(count($h['nights']) > 1 ? ' Nights' : ' Night');

                        $text .= "*{$nightStr}* _at_ *{$h['location']}*\n";
                        $text .= '_Check-in: '.$h['checkIn']->format('d M').'_ & _Check-out: '.$h['checkOut']->format('d M')."_\n";
                        $text .= "*{$h['name']}*\n";
                        $roomCount = ceil($h['pax'] / 2);
                        $text .= "Option 1 • {$roomCount} {$h['room']} ({$h['pax']} Pax)\n\n";
                    }
                }

                // Activities Section
                $entriesByDate = $itinerary->entries()->orderBy('date')->get()->groupBy('date');
                if ($entriesByDate->count() > 0) {
                    $text .= "🚖  *Transportation and Activities*\n";
                    $text .= "-----------\n";
                    $dayNum = 1;
                    foreach ($entriesByDate as $date => $dayEntries) {
                        $carbonDate = Carbon::parse($date);
                        $text .= '*'.$this->getOrdinal($dayNum).' Day - '.$carbonDate->format('D, d M y')."*\n";

                        foreach ($dayEntries as $entry) {
                            if ($entry->entry_type === 'ACTIVITY') {
                                $text .= "• {$entry->description} - Tour _({$itinerary->adult_count} Adults)_\n";
                            } elseif ($entry->entry_type === 'TRANSFER') {
                                $text .= '• TRANSFER '.($entry->transfer_type ?? 'Private')." - Meals/Transit _({$itinerary->adult_count} Adults)_\n";
                            }
                        }
                        $text .= "\n";
                        $dayNum++;
                    }
                }
            }

            if ($request->query('terms', 'false') === 'true') {
                $text .= "*Terms & Conditions:*\n";
                $text .= "Standard cancellation and policies apply. Subject to availability.\n\n";
            }

            $text .= "Looking forward to hearing from you!\n\n";
            $text .= "Warm Regards,\nTIC Tours Team";

            return $this->sendResponse(['text' => $text], 'WhatsApp preview fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Share via Email.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    private function valuesAreEqual($val1, $val2): bool
    {
        if ($val1 === $val2) {
            return true;
        }
        
        // Normalize null, empty string, and 0 for loose comparison if they are equivalent
        $norm1 = ($val1 === null || $val1 === '' || $val1 === 0 || $val1 === 0.0 || $val1 === '0') ? null : $val1;
        $norm2 = ($val2 === null || $val2 === '' || $val2 === 0 || $val2 === 0.0 || $val2 === '0') ? null : $val2;
        if ($norm1 === null && $norm2 === null) {
            return true;
        }

        // Decode JSON if one is string and the other is array/object
        if (is_string($val1) && isset($val1[0]) && ($val1[0] === '[' || $val1[0] === '{')) {
            $decoded = json_decode($val1, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $val1 = $decoded;
            }
        }
        if (is_string($val2) && isset($val2[0]) && ($val2[0] === '[' || $val2[0] === '{')) {
            $decoded = json_decode($val2, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $val2 = $decoded;
            }
        }
        
        if (is_array($val1) && is_array($val2)) {
            // Sort keys to compare
            ksort($val1);
            ksort($val2);
            return json_encode($val1) === json_encode($val2);
        }

        // Normalize date/time strings if they look like dates
        if (is_string($val1) && is_string($val2) && (str_contains($val1, '-') || str_contains($val1, '/')) && (str_contains($val2, '-') || str_contains($val2, '/'))) {
            $t1 = strtotime($val1);
            $t2 = strtotime($val2);
            if ($t1 !== false && $t2 !== false) {
                return $t1 === $t2;
            }
        }
        
        if (is_numeric($val1) && is_numeric($val2)) {
            return (float)$val1 === (float)$val2;
        }
        if (is_bool($val1) || is_bool($val2)) {
            return (bool)$val1 === (bool)$val2;
        }
        return trim((string)$val1) === trim((string)$val2);
    }

    private function hasItineraryChanges(Itinerary $itinerary, array $requestData): bool
    {
        // 1. Check itinerary-level changes
        $fields = [
            'package_name', 'enquiry_id', 'start_date', 'end_date', 'adult_count', 
            'child_count', 'destination_id', 'valid_until', 'price_mode', 'currency'
        ];
        foreach ($fields as $field) {
            if (array_key_exists($field, $requestData)) {
                if (!$this->valuesAreEqual($itinerary->$field, $requestData[$field])) {
                    \Illuminate\Support\Facades\Log::info("Itinerary diff - Field '{$field}': DB = '" . json_encode($itinerary->$field) . "', Request = '" . json_encode($requestData[$field]) . "'");
                    return true;
                }
            }
        }

        // 2. Check entries changes
        $entriesData = $requestData['entries'] ?? [];
        $currentEntries = $itinerary->entries;

        $existingEntryIds = $currentEntries->pluck('id')->toArray();
        $requestEntryIds = [];
        
        foreach ($entriesData as $entry) {
            if (empty($entry['id'])) {
                \Illuminate\Support\Facades\Log::info("Itinerary diff - New entry added (no ID in request)");
                return true;
            }
            $requestEntryIds[] = $entry['id'];
        }

        // Check if any existing entry is deleted
        $deletedIds = array_diff($existingEntryIds, $requestEntryIds);
        if (!empty($deletedIds)) {
            \Illuminate\Support\Facades\Log::info("Itinerary diff - Deleted entries: " . json_encode($deletedIds));
            return true;
        }

        // Compare each entry
        foreach ($entriesData as $entry) {
            $existingEntry = $currentEntries->firstWhere('id', $entry['id']);
            if (!$existingEntry) {
                \Illuminate\Support\Facades\Log::info("Itinerary diff - Entry {$entry['id']} not found in DB");
                return true;
            }

            // We need to compute amount just like in process() because it's calculated on save
            $expectedAmount = 0;
            if (($entry['entry_type'] ?? '') == 'HOTEL') {
                $room = Room::find($entry['room_id']);
                if ($room) {
                    $singlePrice = ($entry['single_count'] ?? 0) * $room->single_bed_amount;
                    $doublePrice = ($entry['double_count'] ?? 0) * $room->double_bed_amount;
                    $triplePrice = ($entry['triple_count'] ?? 0) * $room->triple_bed_amount;
                    $quadPrice = ($entry['quad_count'] ?? 0) * $room->quad_bed_amount;
                    $twoBedroomPrice = ($entry['two_bedroom_count'] ?? 0) * ($room->two_bedroom_amount ?? 0);
                    $threeBedroomPrice = ($entry['three_bedroom_count'] ?? 0) * ($room->three_bedroom_amount ?? 0);
                    $fourBedroomPrice = ($entry['four_bedroom_count'] ?? 0) * ($room->four_bedroom_amount ?? 0);
                    $extraPrice = ($entry['extra_count'] ?? 0) * $room->extra_bed_amount;
                    $childWPrice = ($entry['child_w_count'] ?? 0) * $room->child_w_bed_amount;
                    $childNPrice = ($entry['child_n_count'] ?? 0) * $room->child_n_bed_amount;
                    $expectedAmount = $singlePrice + $doublePrice + $triplePrice + $quadPrice + $twoBedroomPrice + $threeBedroomPrice + $fourBedroomPrice + $extraPrice + $childWPrice + $childNPrice;
                }
            } elseif (($entry['entry_type'] ?? '') == 'ACTIVITY') {
                $activityStartDate = $entry['start_date'] ?? null;
                $activityEndDate = $entry['end_date'] ?? null;
                $activityEstimation = ActivityEstimation::where('activity_id', $entry['subject_id'])->whereDate('from_date', '<=', $activityStartDate)->whereDate('to_date', '>=', $activityEndDate)->first();

                if ($activityEstimation) {
                    $adultCount = (isset($entry['adult_count']) && $entry['adult_count'] !== null) ? $entry['adult_count'] : ($requestData['adult_count'] ?? 0);
                    $childCount = (isset($entry['child_count']) && $entry['child_count'] !== null) ? $entry['child_count'] : ($requestData['child_count'] ?? 0);
                    $expectedAmount = ($activityEstimation->adult_cost * $adultCount) + ($activityEstimation->child_cost * $childCount);
                }
            } elseif (($entry['entry_type'] ?? '') == 'TRANSFER') {
                if (($entry['transfer_type'] ?? '') == 'PRIVATE') {
                    $expectedAmount = $entry['cost'] ?? 0;
                } elseif (($entry['transfer_type'] ?? '') == 'SIC') {
                    $expectedAmount = ($entry['adult_cost'] ?? 0) + ($entry['child_cost'] ?? 0);
                }
            }

            // Compare fields set in process()
            $expectedFields = [
                'date' => $entry['date'] ?? null,
                'entry_type' => $entry['entry_type'] ?? null,
                'start_date' => $entry['start_date'] ?? null,
                'start_time' => $entry['start_time'] ?? null,
                'end_date' => $entry['end_date'] ?? null,
                'end_time' => $entry['end_time'] ?? null,
                'subject_id' => $entry['subject_id'] ?? null,
                'sub_destination_id' => $entry['sub_destination_id'] ?? null,
                'sort_order' => $entry['seq'] ?? 0,
                'amount' => $expectedAmount,
            ];

            if (($entry['entry_type'] ?? '') == 'HOTEL') {
                $expectedFields['option'] = $entry['option'] ?? 'option 1';
                $expectedFields['room_id'] = $entry['room_id'] ?? null;
                $expectedFields['single_count'] = $entry['single_count'] ?? 0;
                $expectedFields['double_count'] = $entry['double_count'] ?? 0;
                $expectedFields['triple_count'] = $entry['triple_count'] ?? 0;
                $expectedFields['quad_count'] = $entry['quad_count'] ?? 0;
                $expectedFields['two_bedroom_count'] = $entry['two_bedroom_count'] ?? 0;
                $expectedFields['three_bedroom_count'] = $entry['three_bedroom_count'] ?? 0;
                $expectedFields['four_bedroom_count'] = $entry['four_bedroom_count'] ?? 0;
                $expectedFields['extra_count'] = $entry['extra_count'] ?? 0;
                $expectedFields['child_w_count'] = $entry['child_w_count'] ?? 0;
                $expectedFields['child_n_count'] = $entry['child_n_count'] ?? 0;
                $expectedFields['room_rows'] = $entry['room_rows'] ?? null;
                $expectedFields['no_of_person'] = $entry['no_of_person'] ?? 0;
                $expectedFields['description'] = $entry['description'] ?? null;
            } elseif (($entry['entry_type'] ?? '') == 'ACTIVITY') {
                $expectedFields['description'] = $entry['description'] ?? null;
                $expectedFields['adult_count'] = $entry['adult_count'] ?? 0;
                $expectedFields['child_count'] = $entry['child_count'] ?? 0;
                $expectedFields['no_of_person'] = $expectedFields['adult_count'] + $expectedFields['child_count'];
            } elseif (($entry['entry_type'] ?? '') == 'TRANSFER') {
                $expectedFields['transfer_type'] = $entry['transfer_type'] ?? null;
                $expectedFields['adult_count'] = $entry['adult_count'] ?? ($requestData['adult_count'] ?? 0);
                $expectedFields['child_count'] = $entry['child_count'] ?? ($requestData['child_count'] ?? 0);
                $expectedFields['no_of_person'] = $expectedFields['adult_count'] + $expectedFields['child_count'];
                $expectedFields['vehicle_count'] = $entry['vehicle_count'] ?? 1;
                $expectedFields['vehicle_type'] = $entry['vehicle_type'] ?? null;
                if (($entry['transfer_type'] ?? '') == 'PRIVATE') {
                    $expectedFields['cost'] = $entry['cost'] ?? 0;
                } elseif (($entry['transfer_type'] ?? '') == 'SIC') {
                    $expectedFields['adult_cost'] = $entry['adult_cost'] ?? 0;
                    $expectedFields['child_cost'] = $entry['child_cost'] ?? 0;
                }
            }

            foreach ($expectedFields as $f => $val) {
                if (!$this->valuesAreEqual($existingEntry->$f, $val)) {
                    \Illuminate\Support\Facades\Log::info("Itinerary diff - Entry ID {$entry['id']} Field '{$f}': DB = '" . json_encode($existingEntry->$f) . "', Expected = '" . json_encode($val) . "'");
                    return true;
                }
            }
        }

        return false;
    }

    private function hasPricingChanges(Itinerary $itinerary, array $requestData): bool
    {
        $pricingFields = [
            'extra_markup_amount', 'extra_markup_percentage', 'cgst_percentage', 'sgst_percentage',
            'igst_percentage', 'tcs_percentage', 'discount_amount', 'currency', 'description',
            'price_mode', 'total_amount', 'grand_total', 'converted_total', 'exchange_rate'
        ];
        
        foreach ($pricingFields as $field) {
            if (array_key_exists($field, $requestData)) {
                if (!$this->valuesAreEqual($itinerary->$field, $requestData[$field])) {
                    \Illuminate\Support\Facades\Log::info("Pricing diff - Field '{$field}': DB = '" . json_encode($itinerary->$field) . "', Request = '" . json_encode($requestData[$field]) . "'");
                    return true;
                }
            }
        }
        
        if (array_key_exists('quoted_options', $requestData)) {
            if (!$this->valuesAreEqual($itinerary->quoted_options, $requestData['quoted_options'])) {
                \Illuminate\Support\Facades\Log::info("Pricing diff - quoted_options: DB = '" . json_encode($itinerary->quoted_options) . "', Request = '" . json_encode($requestData['quoted_options']) . "'");
                return true;
            }
        }

        $entries = $requestData['entries'] ?? [];
        $currentEntries = $itinerary->entries;
        foreach ($entries as $entryData) {
            if (empty($entryData['id'])) {
                \Illuminate\Support\Facades\Log::info("Pricing diff - New entry added (no ID in request)");
                return true;
            }
            $entry = $currentEntries->firstWhere('id', $entryData['id']);
            if (!$entry) {
                \Illuminate\Support\Facades\Log::info("Pricing diff - Entry {$entryData['id']} not found in DB");
                return true;
            }
            
            $expectedAmount = $entryData['amount'];
            $expectedMarkup = $entryData['markup'];
            $expectedBaseAmount = isset($entryData['base_amount']) ? $entryData['base_amount'] : $entryData['amount'];
            $expectedBaseMarkup = isset($entryData['base_markup']) ? $entryData['base_markup'] : $entryData['markup'];
            
            if (!$this->valuesAreEqual($entry->amount, $expectedAmount) ||
                !$this->valuesAreEqual($entry->markup, $expectedMarkup) ||
                !$this->valuesAreEqual($entry->base_amount, $expectedBaseAmount) ||
                !$this->valuesAreEqual($entry->base_markup, $expectedBaseMarkup)) {
                \Illuminate\Support\Facades\Log::info("Pricing diff - Entry ID {$entryData['id']}: DB amount = {$entry->amount}, Req = {$expectedAmount}; DB markup = {$entry->markup}, Req = {$expectedMarkup}");
                return true;
            }
        }

        return false;
    }

    public function shareEmail(Request $request, $id)
    {
        try {
            Validator::make($request->all(), [
                'email' => 'required|email',
                'subject' => 'required|string',
                'html_content' => 'required|string',
            ])->validate();

            $itinerary = Itinerary::findOrFail($id);

            Mail::to($request->email)->send(
                new ShareItineraryMail($request->subject, $request->html_content)
            );

            return $this->sendResponse([], 'Email sent successfully via backend.', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
