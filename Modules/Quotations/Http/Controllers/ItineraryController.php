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
use Modules\Quotations\Entities\PricingSnapshot;
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
use Illuminate\Support\Facades\Mail;
use Modules\Quotations\Emails\ShareItineraryMail;

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

        if (auth()->check()) {
            if (!$id) {
                $requestData['created_by'] = auth()->id();
            }
            $requestData['updated_by'] = auth()->id();
        }

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

            return $this->sendResponse(ItineraryResource::make($itinerary), 'Itinerary Prices Successfully fetched', 200);
        } catch (Exception $exception) {
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
                        'created_by' => $snapshot->creator ? trim($snapshot->creator->first_name . ' ' . $snapshot->creator->last_name) : null,
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

            if (!$data || !isset($data['entries']) || !isset($data['itinerary'])) {
                return $this->sendError('Invalid snapshot data', [], 422);
            }

            // Restore entry amounts & markup
            foreach ($data['entries'] as $entryData) {
                $entry = ItineraryEntry::find($entryData['id']);
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

    public function print(String $id)
    {
        $itinerary = Itinerary::findOrFail($id);

        // Generate a unique filename to prevent browser caching
        $documentFileName = "itinerary_" . $itinerary->id . "_" . time() . ".pdf";

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
            ->header('Content-Disposition', 'inline; filename="' . $documentFileName . '"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Preview HTML response for Email Sharing
     * @param String $id
     * @return JsonResponse
     */
    public function previewHtml(Request $request, String $id)
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
                    'options' => $options,
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
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }

    /**
     * Preview WhatsApp response
     * @param Request $request
     * @param String $id
     * @return JsonResponse
     */
    public function previewWhatsapp(Request $request, String $id)
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

            $text = "Hi " . ($enquiry->customer_name ?? 'Customer') . ",\n\n";
            $text .= "Greetings from *TIC Tours.*\n\n";
            $text .= "Thank you for your query with us. As per your requirements, following are the package details.\n\n";

            $text .= "*Trip ID " . ($enquiry->ref_no ?? $itinerary->seq ?? $itinerary->id) . "*\n";
            $text .= "----------\n";
            $text .= "*" . ($itinerary->package_name ?? 'Package') . "*\n";
            $text .= "• *" . $startDate->format('d M Y') . "* _for_ *{$nightsCount} Nights, {$daysCount} Days*\n";
            $text .= "• *" . $itinerary->adult_count . " Adults*" . ($itinerary->child_count > 0 ? " and " . $itinerary->child_count . " Child" : "") . "\n\n";

            if (!$hideTotalPrice) {
                // ── Resolve currency and grand total ──
                // Step 1: Try to get currency from quoted_options JSON (most accurate — matches UI)
                $currencyCode = 'USD';
                $currencySymbol = '$';
                $finalGrandTotal = floatval($itinerary->grand_total ?? 0);
                $quotedOptions = null;
                $firstOption = null;

                if ($itinerary->quoted_options) {
                    $quotedOptions = is_string($itinerary->quoted_options) ? json_decode($itinerary->quoted_options, true) : $itinerary->quoted_options;
                    if (is_array($quotedOptions) && !empty($quotedOptions)) {
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

                        $isDoubleOrTriple = (stripos($label, 'double') !== false || stripos($label, 'triple') !== false);

                        if ($isDoubleOrTriple && $isPERMode) {
                            // Show per-person rate for sharing types
                            $countSuffix = $count > 1 ? " x {$count}" : "";
                            $text .= "• *{$label}*\t\t{$currencySymbol} " . number_format($perPerson, 2) . $countSuffix . "\n";
                        } else {
                            // Show total for this person type
                            $countSuffix = $count > 1 ? " x {$count}" : "";
                            $text .= "• *{$label}*\t\t- {$currencySymbol} " . number_format($rowTotal, 2) . $countSuffix . "\n";
                        }
                    }
                } else {
                    $text .= "*Price ({$currencyCode}):*\n";
                }

                $total = number_format($finalGrandTotal, 2);
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
                        
                        $nightsKey = $hotelName . '-' . $location;
                        if (!isset($groupedHotels[$nightsKey])) {
                            $groupedHotels[$nightsKey] = [
                                'name' => $hotelName,
                                'location' => $location,
                                'nights' => [],
                                'checkIn' => Carbon::parse($entry->date),
                                'checkOut' => Carbon::parse($entry->date)->addDay(),
                                'room' => $room ? $room->name : 'Room',
                                'meal' => 'Bed and Breakfast', // Default or fetch if available
                                'pax' => $itinerary->adult_count
                            ];
                        }
                        $groupedHotels[$nightsKey]['nights'][] = $index + 1;
                        $groupedHotels[$nightsKey]['checkOut'] = Carbon::parse($entry->date)->addDay();
                    }

                    foreach ($groupedHotels as $h) {
                        $nightOrdinals = array_map([$this, 'getOrdinal'], $h['nights']);
                        $nightStr = implode(', ', $nightOrdinals) . (count($h['nights']) > 1 ? " Nights" : " Night");
                        
                        $text .= "*{$nightStr}* _at_ *{$h['location']}*\n";
                        $text .= "_Check-in: " . $h['checkIn']->format('d M') . "_ & _Check-out: " . $h['checkOut']->format('d M') . "_\n";
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
                        $text .= "*" . $this->getOrdinal($dayNum) . " Day - " . $carbonDate->format('D, d M y') . "*\n";
                        
                        foreach ($dayEntries as $entry) {
                            if ($entry->entry_type === 'ACTIVITY') {
                                $text .= "• {$entry->description} - Tour _({$itinerary->adult_count} Adults)_\n";
                            } elseif ($entry->entry_type === 'TRANSFER') {
                                $text .= "• TRANSFER " . ($entry->transfer_type ?? 'Private') . " - Meals/Transit _({$itinerary->adult_count} Adults)_\n";
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
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
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
