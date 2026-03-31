<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itinerary</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            font-size: 11px;
            color: #4a1c1c;
        }

        * {
            box-sizing: border-box;
        }

        .content {
            padding: 10px 20px;
        }

        /* Greeting */
        .greeting {
            font-weight: bold;
            color: #4a1c1c;
            margin-bottom: 5px;
        }

        .greeting-title {
            font-weight: bold;
            color: #4a1c1c;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .intro-text {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        /* Package Title */
        .package-title {
            font-weight: bold;
            text-decoration: underline;
            color: #4a1c1c;
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* Trip Details */
        .trip-details {
            margin-left: 30px;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .trip-details table td {
            padding: 1px 5px;
            vertical-align: top;
        }

        .trip-label {
            font-weight: bold;
            width: 140px;
        }

        /* Section Headers */
        .section-header {
            font-weight: bold;
            text-decoration: underline;
            color: #4a1c1c;
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        /* Hotel Options Table */
        .option-label {
            font-weight: bold;
            text-decoration: underline;
            color: #4a1c1c;
            margin-top: 12px;
            margin-bottom: 5px;
        }

        .hotel-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 5px;
        }

        .hotel-table th {
            background-color: #d5c6c6;
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            color: #4a1c1c;
        }

        .hotel-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: center;
        }

        /* Rate display */
        .rate-section {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .rate-section .rate-label {
            font-weight: bold;
        }

        /* Tour Cost Includes/Excludes */
        .cost-list {
            margin-left: 15px;
            line-height: 1.8;
        }

        .cost-list li {
            margin-bottom: 2px;
        }

        /* Proposed Itinerary */
        .itinerary-section {
            margin-top: 15px;
        }

        .day-header {
            font-weight: bold;
            color: #4a1c1c;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .day-items {
            margin-left: 120px;
            line-height: 1.6;
        }

        .day-item {
            margin-bottom: 2px;
        }

        /* Important Note */
        .important-note {
            margin-top: 15px;
            line-height: 1.7;
        }

        .highlight {
            background-color: #ffff00;
        }

        /* Cancellation Policy */
        .cancellation-section {
            margin-top: 15px;
        }

        .cancellation-title {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px;
            color: #4a1c1c;
            margin-bottom: 5px;
        }

        .cancellation-items {
            margin-left: 30px;
            line-height: 1.8;
        }

        /* Payment Note */
        .payment-note {
            margin-top: 15px;
        }

        .payment-title {
            font-weight: bold;
            text-decoration: underline;
            color: #4a1c1c;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .payment-list {
            margin-left: 15px;
        }

        .payment-list ol {
            margin: 0;
            padding-left: 20px;
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <div class="content">

        {{-- ============================================ --}}
        {{-- GREETING & INTRO --}}
        {{-- ============================================ --}}
        <p class="greeting">Dear,</p>
        <p class="greeting-title">Greetings from TIC Tours…!!!</p>
        <p class="intro-text">
            Thanks for deciding to avail services from TIC Tours, a leading travel and holidays Management
            Company. We hereby forward you the complete Package Tour plan with all details, for further
            clarification, or change as per your idea or planning please do call or mail us.
        </p>

        {{-- ============================================ --}}
        {{-- PACKAGE TITLE & TRIP DETAILS --}}
        {{-- ============================================ --}}
        @php
            $start = Carbon\Carbon::parse($itinerary->start_date);
            $end = Carbon\Carbon::parse($itinerary->end_date);
            $nightsCount = $end->diffInDays($start);
            $daysCount = $nightsCount + 1;
            $count = $nightsCount . ' N | ' . str_pad($daysCount, 2, '0', STR_PAD_LEFT) . ' D';

            // Calculate quotation validity in days
            $validUntil = Carbon\Carbon::parse($itinerary->valid_until);
            $validityDays = Carbon\Carbon::parse($itinerary->created_at)->diffInDays($validUntil);
        @endphp

        <p class="package-title">{{ $count }} {{ $itinerary->package_name }}</p>

        <div class="trip-details">
            <table>
                <tr>
                    <td class="trip-label">Trip ID:</td>
                    <td>#{{ $itinerary->seq }}</td>
                    <td style="padding-left:30px" class="trip-label">Q/Ref:</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="trip-label">No. of Guests</td>
                    <td>: Adults: {{ $itinerary->adult_count }} &nbsp; Child: {{ $itinerary->child_count }}</td>
                </tr>
                <tr>
                    <td class="trip-label">Traveling Date</td>
                    <td>: {{ date('d M Y', strtotime($itinerary->start_date)) }}</td>
                </tr>
                <tr>
                    <td class="trip-label">No of Night</td>
                    <td>: {{ str_pad($nightsCount, 2, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="trip-label">Quotation Validity</td>
                    <td>: {{ $validityDays }} Days</td>
                </tr>
            </table>
        </div>

        {{-- ============================================ --}}
        {{-- HOTEL OPTIONS SECTION --}}
        {{-- ============================================ --}}
        @php
            $hotelEntries = $itinerary->entries->where('entry_type', 'HOTEL');
            $options = $hotelEntries->groupBy('option');

            $adultCount = $itinerary->adult_count ?? 0;
            $childCount = $itinerary->child_count ?? 0;
            $currencyModel = Modules\Settings\Entities\Currency::find($itinerary->currency);
            $currency = $currencyModel->code ?? 'USD';
        @endphp

        <p class="section-header">Hotel Options with Rate per person on twin sharing basis in {{ $currency }}</p>

        @foreach ($options as $optionName => $optionEntries)
            @php
                $optionIndex = $loop->iteration;

                // Calculate per-person rate for this option
                $adultTotalCost = 0;
                $childWTotalCost = 0;
                $childNTotalCost = 0;

                // Hotel costs for this option
                foreach ($optionEntries as $hotelEntry) {
                    $room = Modules\Settings\Entities\Room::find($hotelEntry->room_id);
                    if ($room) {
                        $adultTotalCost += $room->single_bed_amount + $room->double_bed_amount + $room->triple_bed_amount + $room->extra_bed_amount;
                        $childWTotalCost += $room->child_w_bed_amount * $hotelEntry->child_w_count;
                        $childNTotalCost += $room->child_n_bed_amount * $hotelEntry->child_n_count;
                    }
                }

                // Add transfer & activity costs
                foreach ($itinerary->entries as $entry) {
                    if ($entry->entry_type == 'TRANSFER') {
                        $transferCost = $entry->transfer_type == 'PRIVATE' ? $entry->cost : $entry->adult_cost;
                        $adultTotalCost += $transferCost;
                        $childWTotalCost += $transferCost;
                        $childNTotalCost += $transferCost;
                    }
                    if ($entry->entry_type == 'ACTIVITY') {
                        $activityEstimation = Modules\Settings\Entities\ActivityEstimation::where('activity_id', $entry->subject_id)
                            ->whereDate('from_date', '<=', $entry->start_date)
                            ->whereDate('to_date', '>=', $entry->end_date)
                            ->first();
                        if ($activityEstimation) {
                            $adultTotalCost += $activityEstimation->adult_cost;
                            $childWTotalCost += $activityEstimation->child_cost;
                            $childNTotalCost += $activityEstimation->child_cost;
                        }
                    }
                }

                $adultPerPerson = $adultCount > 0 ? round($adultTotalCost / $adultCount) : 0;
                $childNPerPerson = $childCount > 0 ? round($childNTotalCost / $childCount) : 0;
            @endphp

            <p class="option-label">Option {{ $optionIndex }}</p>
            <table class="hotel-table">
                <thead>
                    <tr>
                        <th width="18%">City/Place</th>
                        <th width="30%">Hotel name</th>
                        <th width="15%">No of Nights</th>
                        <th width="18%">Room Type</th>
                        <th width="19%">Meals Plan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $mergedHotels = [];
                        foreach ($optionEntries as $hotelEntry) {
                            $hotel = Modules\Settings\Entities\Hotel::find($hotelEntry->subject_id);
                            $room = Modules\Settings\Entities\Room::find($hotelEntry->room_id);
                            $subDest = $hotelEntry->sub_destination;

                            $hotelStart = Carbon\Carbon::parse($hotelEntry->start_date);
                            $hotelEnd = Carbon\Carbon::parse($hotelEntry->end_date);
                            $hotelNights = $hotelEnd->diffInDays($hotelStart);

                            $mealPlanText = '';
                            if ($room && $room->meal_plans && $room->meal_plans->count() > 0) {
                                $mealPlanNames = $room->meal_plans->map(function ($mp) {
                                    $plan = Modules\Settings\Entities\MealPlan::find($mp->meal_plan_id);
                                    return $plan ? $plan->name : '';
                                })->filter()->unique()->toArray();
                                $mealPlanText = implode(', ', $mealPlanNames);
                            }

                            $key = ($subDest->id ?? 0) . '_' . ($hotel->id ?? 0) . '_' . ($room->id ?? 0);
                            if (isset($mergedHotels[$key])) {
                                $mergedHotels[$key]['nights'] += $hotelNights;
                            } else {
                                $mergedHotels[$key] = [
                                    'city' => optional($subDest)->name ?? optional($hotel?->sub_destination)->name ?? '',
                                    'hotel' => optional($hotel)->name ?? 'N/A',
                                    'nights' => $hotelNights,
                                    'room' => optional($room?->room_type)->name ?? '',
                                    'meals' => $mealPlanText
                                ];
                            }
                        }
                    @endphp
                    @foreach ($mergedHotels as $mh)
                        <tr>
                            <td>{{ $mh['city'] }}</td>
                            <td>{{ $mh['hotel'] }}</td>
                            <td>{{ $mh['nights'] }}</td>
                            <td>{{ $mh['room'] }}</td>
                            <td>{{ $mh['meals'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="rate-section">
                <span class="rate-label">Rate</span><br>
                {{ $currency }} {{ number_format($adultPerPerson) }} per person on double/twin sharing x {{ $adultCount }} pax
                @if($childCount > 0)
                <br>
                {{ $currency }} {{ number_format($childNPerPerson) }} per child without bed
                @endif
            </div>
        @endforeach

        {{-- ============================================ --}}
        {{-- TOUR COST INCLUDES --}}
        {{-- ============================================ --}}
        <p class="section-header">Tour Cost Includes:</p>
        <ul class="cost-list">
            @php
                $uniqueDates = array_unique($itinerary->entries->pluck('date')->toArray());
                sort($uniqueDates);
            @endphp

            {{-- List all hotel accommodations --}}
            @foreach ($options as $optionName => $optionEntries)
                @php
                    $mergedHotelsList = [];
                    foreach ($optionEntries as $hotelEntry) {
                        $hotel = Modules\Settings\Entities\Hotel::find($hotelEntry->subject_id);
                        $room = Modules\Settings\Entities\Room::find($hotelEntry->room_id);
                        $hotelStart = Carbon\Carbon::parse($hotelEntry->start_date);
                        $hotelEnd = Carbon\Carbon::parse($hotelEntry->end_date);
                        $hotelNights = $hotelEnd->diffInDays($hotelStart);

                        $mealPlanText = '';
                        if ($room && $room->meal_plans && $room->meal_plans->count() > 0) {
                            $mealPlanNames = $room->meal_plans->map(function ($mp) {
                                $plan = Modules\Settings\Entities\MealPlan::find($mp->meal_plan_id);
                                return $plan ? $plan->name : '';
                            })->filter()->unique()->toArray();
                            $mealPlanText = ' with ' . implode(', ', $mealPlanNames);
                        }

                        $roomTypeName = optional($room?->room_type)->name ?? 'mentioned';
                        $key = ($hotel->id ?? 0) . '_' . ($room->id ?? 0);
                        
                        if (isset($mergedHotelsList[$key])) {
                            $mergedHotelsList[$key]['nights'] += $hotelNights;
                        } else {
                            $mergedHotelsList[$key] = [
                                'nights' => $hotelNights,
                                'room' => $roomTypeName,
                                'meals' => $mealPlanText
                            ];
                        }
                    }
                @endphp
                @foreach ($mergedHotelsList as $mhl)
                    <li>{{ $mhl['nights'] }} Night accommodation in BASIC/{{ $mhl['room'] }} category room{{ $mhl['meals'] }}</li>
                @endforeach
                @php break; @endphp {{-- Only show first option's inclusions --}}
            @endforeach

            {{-- List transfers --}}
            @foreach ($itinerary->entries->where('entry_type', 'TRANSFER') as $transferEntry)
                @php
                    $transfer = Modules\Settings\Entities\Transfer::find($transferEntry->subject_id);
                    $transferType = $transferEntry->transfer_type == 'PRIVATE' ? 'PVT' : 'SIC';
                @endphp
                <li>Transfer from {{ optional($transfer)->description ?? optional($transfer)->vehicle_name }} by {{ $transferType }}</li>
            @endforeach

            {{-- List activities --}}
            @foreach ($itinerary->entries->where('entry_type', 'ACTIVITY') as $activityEntry)
                @php
                    $activity = Modules\Settings\Entities\Activity::find($activityEntry->subject_id);
                    $transferType = '';
                @endphp
                <li>{{ optional($activity)->activity_name }}{{ $activityEntry->description ? ' - ' . $activityEntry->description : '' }}</li>
            @endforeach

            <li>English speaking customer service assistance</li>
        </ul>

        {{-- ============================================ --}}
        {{-- PROPOSED ITINERARY --}}
        {{-- ============================================ --}}
        <p class="section-header" style="font-size:12px; margin-top:20px;">Proposed Itinerary</p>

        <div class="itinerary-section">
            @foreach ($uniqueDates as $key => $date)
                @php
                    $dayEntries = $itinerary->entries->where('date', $date);
                    $dateFormatted = date('d M', strtotime($date));
                    
                    $visibleItems = [];
                    foreach ($dayEntries as $item) {
                        if ($item->entry_type == 'HOTEL') {
                            $sub = Modules\Settings\Entities\Hotel::find($item->subject_id);
                            $room = $item->room;
                            $mealPlanText = '';
                            if ($room && $room->meal_plans && $room->meal_plans->count() > 0) {
                                $mealPlanNames = $room->meal_plans->map(function ($mp) {
                                    $plan = Modules\Settings\Entities\MealPlan::find($mp->meal_plan_id);
                                    return $plan ? $plan->name : '';
                                })->filter()->unique()->toArray();
                                $mealPlanText = implode(', ', $mealPlanNames);
                            }
                            
                            $str = $mealPlanText ? 'Breakfast at hotel' : 'Check-in at ' . optional($sub)->name;
                            if (!in_array($str, $visibleItems)) {
                                $visibleItems[] = $str;
                            }
                        } elseif ($item->entry_type == 'TRANSFER') {
                            $sub = Modules\Settings\Entities\Transfer::find($item->subject_id);
                            $transferType = $item->transfer_type == 'PRIVATE' ? 'PVT' : 'SIC';
                            $visibleItems[] = 'Transfer from ' . (optional($sub)->description ?? optional($sub)->vehicle_name) . ' by ' . $transferType . ' basis';
                        } elseif ($item->entry_type == 'ACTIVITY') {
                            $sub = Modules\Settings\Entities\Activity::find($item->subject_id);
                            $str = optional($sub)->activity_name;
                            if ($item->description) {
                                $str .= ' - ' . $item->description;
                            }
                            $visibleItems[] = $str;
                        }
                    }
                @endphp
                <div style="margin-bottom: 8px;">
                    <span class="day-header">Day {{ $key + 1 }} ({{ $dateFormatted }}) :</span>
                    @foreach ($visibleItems as $index => $text)
                        @if ($index == 0)
                            <span>{{ $text }}</span><br>
                        @else
                            <span style="margin-left: 120px;">: {{ $text }}</span><br>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- ============================================ --}}
        {{-- TOUR COST EXCLUDES --}}
        {{-- ============================================ --}}
        <p class="section-header">Tour Cost Excludes:</p>
        <ul class="cost-list" style="list-style-type: '· ';">
            <li>Any Airfare / Visa fee</li>
            <li>Our services cover accidental insurance, if any hospital case guests have to pay and the insurance company will reimburse after checking the documents. It is advisable for you to take an insurance cover for total travel including air.</li>
            <li>Any meals other than those mentioned in Menu.</li>
            <li>Any portage at airports and hotels, tips, insurance, wine, mineral water, telephone charges, and all items of personal nature.</li>
            <li>Expenses caused by factors beyond our control like rail and flight delays, roadblocks, vehicle mal-functions, political disturbances etc.</li>
            <li>Any services not specifically mentioned in the inclusions.</li>
        </ul>

        {{-- ============================================ --}}
        {{-- IMPORTANT NOTE --}}
        {{-- ============================================ --}}
        <div class="important-note">
            <p class="section-header">Important Note:</p>
            <div style="margin-left: 15px; line-height: 1.8;">
                · <span class="highlight"><strong>The above package is only an offer and not a confirmation</strong></span>. <em>We shall proceed with your booking only after we receive your confirmation.</em><br>
                · The airfare quoted, if any, is as of now and is subject to change.<br>
                · In case of non-availability of rooms at the hotels mentioned, we shall provide you alternate hotels of similar category.<br>
                · <span class="highlight">Check-in time at the hotel is 14:00/15:00 hrs & Check-out time at the hotel is 11:00/12.00 hrs As per the hotel policy</span><br>
                · Booking confirmation is subject to availability.<br>
                · The above rates are valid for the mentioned period only.<br>
                · 100% Package cost should be paid <u>07 Days</u> prior to departure or mentioned cut off date<br>
                · TIC Tours Reserves the right to change/modify or terminate the offer any time at its own discretion and without any prior notice.<br>
                · <span class="highlight">Infants are free (Below 02 years/below 90 CM), If the height is more than 90 CM is considered as a child & The Child height is more than 120/130 CM is considered as an adult and pay the difference at the counter directly.</span><br>
                · All the Island Tours and ferry transfers depend on the weather condition. If Pattaya Coral Island does not operate due to Heavy Waves/Wind/Thunderstorm we can refund THB 200/USD 6 Per person only.<br>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- CANCELLATION POLICY --}}
        {{-- ============================================ --}}
        <div class="cancellation-section">
            <p class="cancellation-title">CANCELLATION POLICY</p>
            <p>Cancellation charges per person will be applicable as follows:</p>
            <div class="cancellation-items">
                · If cancellation is made any time not less than 16 days prior to departure, 20-30% of Package Cost shall be deducted.<br>
                · If cancellation is made 15 to 07 days prior to departure, 50-60% of tour cost shall be deducted.<br>
                · If cancellation is made 06 to 03 day prior to departure, 75-85% of tour cost shall be deducted.<br>
                · In case a passenger is no show at the time of departure, 100% of tour cost shall be deducted.<br>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- PAYMENT NOTE --}}
        {{-- ============================================ --}}
        <div class="payment-note">
            <p class="payment-title">Payment Note:</p>
            <div class="payment-list">
                <ol>
                    <li>Online Payment available - use Credit/Debit Card - <strong>No Additional charge</strong></li>
                    <li>For Indian Payment 5% GST is applicable and ROE will be <u>xe.com</u> +1.5 on the day of deposit. TCS Declaration is required</li>
                    <li>Every swift transaction adds USD 35 as bank charges. <u>Outward Remittance charge to be borne by Transferor</u></li>
                    <li>Confirmed tour vouchers will be issued 3 days before the travel date.</li>
                </ol>
            </div>
        </div>

    </div>
</body>

</html>
