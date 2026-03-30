<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TIC Tours - Itinerary</title>
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            font-size: 11px;
            margin: 20px;
            color: #222;
        }

        h3 {
            margin: 0 0 4px 0;
        }

        p {
            margin: 4px 0;
            text-align: justify;
        }

        .section-title {
            background-color: #000;
            color: #fff;
            padding: 6px 10px;
            font-weight: 700;
            font-size: 12px;
            margin-top: 12px;
            margin-bottom: 0;
        }

        /* ── Query Details Table ── */
        .border-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .border-table th,
        .border-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .border-table .header-row td {
            background-color: #000;
            color: #fff;
            font-weight: 700;
        }

        /* ── Hotel Options Table ── */
        .hotel-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 6px;
        }

        .hotel-table th {
            background-color: #cfe2f3;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        .hotel-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .rate-box {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            padding: 8px 10px;
            margin-top: 4px;
            margin-bottom: 10px;
        }

        /* ── Itinerary Day Rows ── */
        .day-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .day-table td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .day-date {
            background-color: #e2e2e2;
            width: 20%;
            font-weight: 600;
        }

        .day-content {
            background-color: #f5f5f5;
            width: 80%;
        }

        /* ── Inclusions / Exclusions ── */
        .list-section {
            margin-top: 4px;
            padding-left: 14px;
        }

        .list-section li {
            margin-bottom: 3px;
        }

        /* ── Notes / Policy blocks ── */
        .note-block {
            margin-top: 6px;
            font-size: 10.5px;
        }

        .note-block p,
        .note-block li {
            margin-bottom: 3px;
        }

        .highlight-package {
            background-color: #d9ead3;
            padding: 4px 8px;
            font-weight: 700;
            font-size: 13px;
            display: inline-block;
            margin: 8px 0;
        }

        .option-label {
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 11.5px;
        }
    </style>
</head>

<body>

    {{-- ══════════════════════════════════════════
         GREETING
    ══════════════════════════════════════════ --}}
    <p>Dear {{ $itinerary->enquiry->customer->name ?? 'Sir/Madam' }},</p>
    <p>Greetings from TIC Tours…!!!</p>
    <p>
        Thanks for deciding to avail services from TIC Tours, a leading travel and holidays Management Company.
        We hereby forward you the complete Package Tour plan with all details, for further clarification,
        or change as per your idea or planning please do call or mail us.
    </p>

    {{-- ══════════════════════════════════════════
         PACKAGE TITLE
    ══════════════════════════════════════════ --}}
    @php
        $start     = \Carbon\Carbon::parse($itinerary->enquiry->start_date);
        $end       = \Carbon\Carbon::parse($itinerary->enquiry->end_date);
        $daysCount = $end->diffInDays($start);
        $nightCount = $daysCount - 1;
        $countLabel = $nightCount . ' N | ' . $daysCount . ' D';
    @endphp

    <div class="highlight-package">{{ $countLabel }} {{ $itinerary->package_name }}</div>

    {{-- ══════════════════════════════════════════
         QUERY DETAILS
    ══════════════════════════════════════════ --}}
    <div class="section-title">Query Details</div>
    <table class="border-table">
        <tr>
            <td width="22%"><strong>Trip ID / Q Ref:</strong></td>
            <td width="28%">#{{ $itinerary->seq }}</td>
            <td width="22%"><strong>No. of Nights:</strong></td>
            <td width="28%">{{ $nightCount }}</td>
        </tr>
        <tr>
            <td><strong>No. of Guests:</strong></td>
            <td>{{ $itinerary->adult_count }} Adult + {{ $itinerary->child_count }} Child</td>
            <td><strong>Quotation Validity:</strong></td>
            <td>7 Days</td>
        </tr>
        <tr>
            <td><strong>Destination:</strong></td>
            <td>{{ $itinerary->destination->name ?? 'N/A' }}</td>
            <td><strong>Traveling Date:</strong></td>
            <td>{{ date('d M Y', strtotime($itinerary->start_date)) }}</td>
        </tr>
        <tr>
            <td><strong>Query Date:</strong></td>
            <td>{{ date('D, d M Y', strtotime($itinerary->created_at)) }}</td>
            <td><strong>End Date:</strong></td>
            <td>{{ date('D, d M Y', strtotime($itinerary->end_date)) }}</td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════
         HOTEL OPTIONS WITH RATES
    ══════════════════════════════════════════ --}}
    <div class="section-title">Hotel Options with Rate per person on twin sharing basis in USD</div>

    @php
        $hotels = $itinerary->entries->where('entry_type', 'HOTEL');
        $optionNumber = 0;

        /* ── Pre-calculate per-person costs ── */
        $adultCount = $itinerary?->enquiry?->adult_count ?? 0;
        $childCount = $itinerary?->enquiry?->child_count  ?? 0;

        $adulltPerPersonNetAmount = 0;
        $childWPerPersonNetAmount = 0;
        $childNPerPersonNetAmount = 0;

        foreach ($itinerary->entries as $entry) {
            if ($entry->entry_type == 'HOTEL') {
                $room = \Modules\Settings\Entities\Room::find($entry->room_id);
                $adulltPerPersonNetAmount += $room->single_bed_amount + $room->double_bed_amount + $room->triple_bed_amount + $room->extra_bed_amount;
                $childWPerPersonNetAmount += $room->child_w_bed_amount * $entry->child_w_count;
                $childNPerPersonNetAmount += $room->child_n_bed_amount * $entry->child_n_count;
            }
            if ($entry->entry_type == 'TRANSFER') {
                $transferCost = $entry->transfer_type == 'PRIVATE' ? $entry->cost : $entry->adult_cost;
                $adulltPerPersonNetAmount += $transferCost;
                $childWPerPersonNetAmount += $transferCost;
                $childNPerPersonNetAmount += $transferCost;
            }
            if ($entry->entry_type == 'ACTIVITY') {
                $activityEstimation = \Modules\Settings\Entities\ActivityEstimation::where('activity_id', $entry['subject_id'])
                    ->whereDate('from_date', '<=', $entry['start_date'])
                    ->whereDate('to_date',   '>=', $entry['end_date'])
                    ->first();
                if ($activityEstimation) {
                    $adulltPerPersonNetAmount += $activityEstimation->adult_cost;
                    $childWPerPersonNetAmount += $activityEstimation->child_cost;
                    $childNPerPersonNetAmount += $activityEstimation->child_cost;
                }
            }
        }

        $adultPerPerson  = $adultCount  ? round($adulltPerPersonNetAmount / $adultCount, 2)  : 0;
        $childWPerPerson = $childCount  ? round($childWPerPersonNetAmount  / $childCount, 2)  : 0;
        $childNPerPerson = $childCount  ? round($childNPerPersonNetAmount  / $childCount, 2)  : 0;
    @endphp

    @foreach ($hotels as $hotelEntry)
        @php
            $optionNumber++;
            $hotel = \Modules\Settings\Entities\Hotel::find($hotelEntry->subject_id);
            $room  = \Modules\Settings\Entities\Room::find($hotelEntry->room_id);

            /* Meal plans for this hotel entry */
            $mealPlanNames = [];
            if ($room && $room->meal_plans) {
                foreach ($room->meal_plans->toArray() as $mp) {
                    $plan = \Modules\Settings\Entities\MealPlan::find($mp['meal_plan_id']);
                    if ($plan) $mealPlanNames[] = $plan->name;
                }
            }
            $mealPlansStr = implode(', ', $mealPlanNames);
        @endphp

        <div class="option-label">Option {{ $optionNumber }}</div>
        <table class="hotel-table">
            <thead>
                <tr>
                    <th>City / Place</th>
                    <th>Hotel Name</th>
                    <th>No of Nights</th>
                    <th>Room Type</th>
                    <th>Meal Plan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $itinerary->destination->name ?? 'N/A' }}</td>
                    <td>{{ $hotel->name ?? 'N/A' }}</td>
                    <td>{{ $hotelEntry->nights ?? $nightCount }}</td>
                    <td>{{ optional($room->room_type)->name ?? $hotelEntry->option ?? 'N/A' }}</td>
                    <td>{{ $mealPlansStr ?: 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="rate-box">
            <strong>Rate</strong><br>
            USD {{ $adultPerPerson }} per person on double/twin sharing &times; {{ $adultCount }} pax<br>
            @if ($childCount > 0)
                USD {{ $childWPerPerson }} per child with extra bed<br>
                USD {{ $childNPerPerson }} per child without bed
            @endif
        </div>
    @endforeach

    {{-- ══════════════════════════════════════════
         TOUR COST INCLUDES
    ══════════════════════════════════════════ --}}
    <div class="section-title">Tour Cost Includes:</div>
    <ul class="list-section">
        <li>{{ $nightCount }} Night accommodation in mentioned category room with breakfast</li>
        @foreach ($itinerary->entries->where('entry_type', 'TRANSFER') as $transfer)
            @php $t = \Modules\Settings\Entities\Transfer::find($transfer->subject_id); @endphp
            <li>Transfer via {{ optional($t)->vehicle_name ?? 'N/A' }} ({{ $transfer->transfer_type }})</li>
        @endforeach
        @foreach ($itinerary->entries->where('entry_type', 'ACTIVITY') as $activity)
            @php $a = \Modules\Settings\Entities\Activity::find($activity->subject_id); @endphp
            <li>{{ optional($a)->activity_name ?? 'N/A' }}</li>
        @endforeach
        <li>Comp: Thai Accidental Insurance</li>
        <li>English speaking customer service assistance</li>
    </ul>

    {{-- ══════════════════════════════════════════
         PROPOSED ITINERARY
    ══════════════════════════════════════════ --}}
    <div class="section-title">Proposed Itinerary</div>

    @php
        $uniqueDates = array_unique($itinerary->entries->pluck('date')->toArray());
        sort($uniqueDates);
    @endphp

    @foreach ($uniqueDates as $key => $date)
        <table class="day-table">
            <tr>
                <td class="day-date">{{ date('d M Y', strtotime($date)) }}</td>
                <td class="day-content">
                    <strong>Day {{ $key + 1 }}</strong><br>
                    @foreach ($itinerary->entries->where('date', $date) as $item)
                        @php
                            if ($item->entry_type == 'HOTEL') {
                                $sub = \Modules\Settings\Entities\Hotel::find($item->subject_id);
                                $mealArr = $item->room->meal_plans->toArray();
                                $mpNames = array_map(function($mp) {
                                    $p = \Modules\Settings\Entities\MealPlan::find($mp['meal_plan_id']);
                                    return optional($p)->name;
                                }, $mealArr);
                                $mpStr = implode(', ', array_filter($mpNames));
                                $line = 'Accommodation in ' . optional($sub)->name
                                      . ' (' . optional($item->room->room_type)->name . ')'
                                      . ($mpStr ? ' with Meal (' . $mpStr . ')' : '');
                                echo $line;
                            } elseif ($item->entry_type == 'TRANSFER') {
                                $sub = \Modules\Settings\Entities\Transfer::find($item->subject_id);
                                echo 'Transfer via ' . optional($sub)->vehicle_name;
                            } elseif ($item->entry_type == 'ACTIVITY') {
                                $sub = \Modules\Settings\Entities\Activity::find($item->subject_id);
                                echo optional($sub)->activity_name;
                            }
                        @endphp
                        <br>
                    @endforeach
                </td>
            </tr>
        </table>
    @endforeach

    {{-- ══════════════════════════════════════════
         TOUR COST EXCLUDES
    ══════════════════════════════════════════ --}}
    <div class="section-title">Tour Cost Excludes:</div>
    <ul class="list-section">
        <li>Any Airfare / Visa fee</li>
        <li>Our services cover accidental insurance; if any hospital case guests have to pay and the insurance company will reimburse after checking the documents. It is advisable for you to take an insurance cover for total travel including air.</li>
        <li>Any meals other than those mentioned in the menu.</li>
        <li>Any portage at airports and hotels, tips, insurance, wine, mineral water, telephone charges, and all items of personal nature.</li>
        <li>Expenses caused by factors beyond our control like rail and flight delays, roadblocks, vehicle malfunctions, political disturbances, etc.</li>
        <li>Any services not specifically mentioned in the inclusions.</li>
    </ul>

    {{-- ══════════════════════════════════════════
         IMPORTANT NOTE
    ══════════════════════════════════════════ --}}
    <div class="section-title">Important Note:</div>
    <div class="note-block">
        <ul class="list-section">
            <li>The above package is only an offer and not a confirmation. We shall proceed with your booking only after we receive your confirmation.</li>
            <li>The airfare quoted, if any, is as of now and is subject to change.</li>
            <li>In case of non-availability of rooms at the hotels mentioned, we shall provide you alternate hotels of similar category.</li>
            <li>Check-in time at the hotel is 14:00/15:00 hrs &amp; Check-out time is 11:00/12:00 hrs as per the hotel policy.</li>
            <li>Booking confirmation is subject to availability.</li>
            <li>The above rates are valid for the mentioned period only.</li>
            <li>100% Package cost should be paid 07 Days prior to departure or mentioned cut-off date.</li>
            <li>TIC Tours reserves the right to change/modify or terminate the offer any time at its own discretion and without any prior notice.</li>
            <li>Infants are free (Below 02 years / below 90 CM). If the height is more than 90 CM it is considered as a child &amp; the child height more than 120/130 CM is considered as an adult and pay the difference at the counter directly.</li>
            <li>All island tours and ferry transfers depend on the weather condition. If Pattaya Coral Island does not operate due to heavy waves/wind/thunderstorm we can refund THB 200 / USD 6 per person only.</li>
            <li>Normal Lunch/Dinner Menu: 2 Veg Main + 1 Non-Veg Main (Chicken or Fish) + 1 Dal + 1 Rice + Roti/Naan + Salad + Pickle + 1 Dessert + Drinking water.</li>
        </ul>
    </div>

    {{-- ══════════════════════════════════════════
         CANCELLATION POLICY
    ══════════════════════════════════════════ --}}
    <div class="section-title">Cancellation Policy</div>
    <div class="note-block">
        <ul class="list-section">
            <li>If cancellation is made any time not less than 16 days prior to departure, 20–30% of Package Cost shall be deducted.</li>
            <li>If cancellation is made 15 to 07 days prior to departure, 50–60% of tour cost shall be deducted.</li>
            <li>If cancellation is made 06 to 03 days prior to departure, 75–85% of tour cost shall be deducted.</li>
            <li>In case a passenger is no show at the time of departure, 100% of tour cost shall be deducted.</li>
        </ul>
    </div>

    {{-- ══════════════════════════════════════════
         PAYMENT NOTE
    ══════════════════════════════════════════ --}}
    <div class="section-title">Payment Note:</div>
    <div class="note-block">
        <ol class="list-section">
            <li>Online Payment available – use Credit/Debit Card – No Additional charge.</li>
            <li>For Indian Payment 5% GST is applicable and ROE will be xe.com +1.5 on the day of deposit. TCS Declaration is required.</li>
            <li>Every swift transaction adds USD 35 as bank charges. Outward Remittance charge to be borne by Transferor.</li>
            <li>Confirmed tour vouchers will be issued 3 days before the travel date.</li>
        </ol>
    </div>

</body>
</html>