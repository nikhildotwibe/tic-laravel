<!DOCTYPE html>
<html>
@php
    $opts = $options ?? [
        'priceBreakup' => true,
        'hideTotalPrice' => false,
        'itinerary' => true,
        'terms' => true,
    ];
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

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
                    <td>{{ optional($itinerary->enquiry)->ref_no }}</td>
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
            
            // Handle currency lookup (ID, 'base' string, or fallback)
            // If the frontend passes a currency_code override (e.g. MYR selected on Pricing page),
            // use it directly — this fixes the email showing the stale DB currency (e.g. THB).
            if (!empty($currencyOverride ?? null)) {
                $currency = $currencyOverride;
            } elseif ($itinerary->currency === 'base' || empty($itinerary->currency)) {
                // Fetch from enquiry base currency if available, otherwise default to 'THB'
                $currency = optional($itinerary->enquiry)->currency_code ?? 'THB';
            } else {
                $currencyModel = Modules\Settings\Entities\Currency::find($itinerary->currency);
                $currency = $currencyModel->to_currency ?? $currencyModel->code ?? $itinerary->currency ?? 'THB';
            }
        @endphp

        @if($opts['hideTotalPrice'])
        <p class="section-header">Hotel Options</p>
        @else
        <p class="section-header">Hotel Options with Rate per person on twin sharing basis in {{ $currency }}</p>
        @endif

        @php
            $quotedOptionsStr = $itinerary->quoted_options;
            $quotedOptions = is_string($quotedOptionsStr) ? json_decode($quotedOptionsStr, true) : ($quotedOptionsStr ?: []);

            $optionLabels = $options->keys()->toArray();
            $optCount = max(count($optionLabels), count($quotedOptions));
            if ($optCount == 0) {
                $optCount = 1;
            }
        @endphp

        @for ($i = 0; $i < $optCount; $i++)
            @php
                $optionIndex = $i + 1;
                $optionName = $optionLabels[$i] ?? ('Option ' . $optionIndex);
                $optionEntries = isset($options[$optionName]) ? $options[$optionName] : collect();

                // 1. Calculate base total for this option (Hotels + Transfers + Activities)
                $optionBaseAmount = 0;
                
                // Add amounts for hotels in THIS option
                foreach ($optionEntries as $hotelEntry) {
                    $optionBaseAmount += ($hotelEntry->amount ?? 0) + ($hotelEntry->markup ?? 0);
                }

                // Add amounts for all other entries (Transfers, Activities) which are common across options
                foreach ($itinerary->entries as $entry) {
                    if ($entry->entry_type != 'HOTEL') {
                        $optionBaseAmount += ($entry->amount ?? 0) + ($entry->markup ?? 0);
                    }
                }

                // 2. Apply Itinerary-level Markup
                $extraMarkup = 0;
                if ($itinerary->extra_markup_percentage > 0) {
                    $extraMarkup = $optionBaseAmount * ($itinerary->extra_markup_percentage / 100);
                } else {
                    $extraMarkup = $itinerary->extra_markup_amount ?? 0;
                }
                $optionTotalWithMarkup = $optionBaseAmount + $extraMarkup;

                // 3. Apply Discount
                $discount = $itinerary->discount_amount ?? 0;
                $optionTotalWithDiscount = $optionTotalWithMarkup - $discount;

                // 4. Apply Taxes
                $taxPercent = ($itinerary->cgst_percentage ?? 0) + ($itinerary->sgst_percentage ?? 0) + ($itinerary->igst_percentage ?? 0);
                $taxAmount = $optionTotalWithDiscount * ($taxPercent / 100);
                $optionGrandTotal = $optionTotalWithDiscount + $taxAmount;

                // 5. Apply Exchange Rate and Calculate Per Person
                $rate = $itinerary->exchange_rate ?: 1;
                $totalPax = ($adultCount + $childCount) ?: 1;
                
                // Final Grand Total in converted currency
                $convertedGrandTotal = $optionGrandTotal / $rate;
                
                // Per-person distribution (simplified match to frontend logic)
                if ($itinerary->price_mode == "TOTAL_PRICE") {
                    $adultPerPerson = $convertedGrandTotal;
                    $childNPerPerson = $convertedGrandTotal;
                } else {
                    $adultPerPerson = $adultCount > 0 ? round($convertedGrandTotal / $totalPax) : 0;
                    $childNPerPerson = $childCount > 0 ? round($convertedGrandTotal / $totalPax) : 0;
                }
            @endphp

            <p class="option-label">Option {{ $optionIndex }}</p>
            @if(count($optionEntries) > 0)
            <table class="hotel-table">
                <thead>
                    <tr>
                        <th width="15%">City/Place</th>
                        <th width="28%">Hotel name</th>
                        <th width="10%">No of Nights</th>
                        <th width="15%">Room Type</th>
                        <th width="16%">Check In</th>
                        <th width="16%">Check Out</th>
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

                            $key = ($subDest->id ?? 0) . '_' . ($hotel->id ?? 0) . '_' . ($room->id ?? 0);
                            if (!isset($mergedHotels[$key])) {
                                $mergedHotels[$key] = [
                                    'city'       => optional($subDest)->name ?? optional($hotel?->sub_destination)->name ?? '',
                                    'hotel'      => optional($hotel)->name ?? 'N/A',
                                    'room'       => optional($room?->room_type)->name ?? '',
                                    'check_in'   => $hotelStart,
                                    'check_out'  => $hotelEnd,
                                    'nights_dates' => [],
                                ];
                            }

                            if ($hotelStart->lt($mergedHotels[$key]['check_in'])) {
                                $mergedHotels[$key]['check_in'] = $hotelStart;
                            }
                            if ($hotelEnd->gt($mergedHotels[$key]['check_out'])) {
                                $mergedHotels[$key]['check_out'] = $hotelEnd;
                            }

                            $days = $hotelEnd->diffInDays($hotelStart);
                            for ($d = 0; $d < $days; $d++) {
                                $mergedHotels[$key]['nights_dates'][] = $hotelStart->copy()->addDays($d)->format('Y-m-d');
                            }
                        }

                        foreach ($mergedHotels as &$mh) {
                            $mh['nights'] = count(array_unique($mh['nights_dates']));
                            $mh['check_in'] = $mh['check_in']->format('d M Y');
                            $mh['check_out'] = $mh['check_out']->format('d M Y');
                        }
                        unset($mh);
                    @endphp
                    @foreach ($mergedHotels as $mh)
                        <tr>
                            <td>{{ $mh['city'] }}</td>
                            <td>{{ $mh['hotel'] }}</td>
                            <td>{{ $mh['nights'] }}</td>
                            <td>{{ $mh['room'] }}</td>
                            <td>{{ $mh['check_in'] }}</td>
                            <td>{{ $mh['check_out'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if(!$opts['hideTotalPrice'])
            @php
                // Look up the matching quoted option for this iteration (needed for both breakup rows and total)
                $matchedQOpt = null;
                if ($quotedOptions) {
                    $idx = $i;
                    if (isset($quotedOptions[$idx])) {
                        $matchedQOpt = $quotedOptions[$idx];
                    }
                }
                // Option A: accumulate floor(perPerson)*count so the total always matches what is shown
                $displayGrandTotal = 0;
            @endphp
            <div class="rate-section">
                <span class="rate-label">Rate</span><br>
                @if($opts['priceBreakup'])
                    @if($matchedQOpt && !empty($matchedQOpt['rows']))
                        @foreach($matchedQOpt['rows'] as $row)
                            @php
                                $rawCount    = isset($row['count']) && $row['count'] > 0 ? (int)$row['count'] : 1;
                                $rowKey      = $row['key'] ?? '';
                                $label       = $row['label'] ?? 'Person';

                                // row['total'] is the grand total for ALL persons of this type.
                                // row['count'] is the actual number of persons (e.g. 4 for 4 pax in double).
                                // perPerson = total / count  — simple and correct.
                                $rowTotal    = (float)($row['total'] ?? 0);
                                $personCount = $rawCount; // display count = actual person count
                                $perPerson   = $rawCount > 0 ? $rowTotal / $rawCount : 0;

                                // Accumulate grand total
                                $displayGrandTotal += floor($perPerson) * $personCount;
                            @endphp
                            {{ $currency }} {{ number_format(floor($perPerson), 0) }} per {{ $label }}
                            @if($personCount > 0)
                                * {{ $personCount }}
                            @endif
                            <br>
                        @endforeach
                    @else
                        @if ($itinerary->price_mode == "TOTAL_PRICE")
                            {{ $currency }} {{ number_format(floor($adultPerPerson), 0) }} on double/twin sharing basis
                        @else
                            {{ $currency }} {{ number_format(floor($adultPerPerson), 0) }} per person on double/twin sharing basis
                        @endif
                        @if($totalPax > 1)
                            * {{ $totalPax }}
                        @endif
                        <br>
                    @endif
                @endif
                @php
                    // Option A: $displayGrandTotal was built from floor(perPerson)*count above.
                    // Fall back to saved grandTotal only when no rows were available
                    // (e.g. priceBreakup is disabled or quoted_options has no rows data).
                    if ($displayGrandTotal == 0) {
                        $displayGrandTotal = ($matchedQOpt && isset($matchedQOpt['grandTotal']))
                            ? $matchedQOpt['grandTotal']
                            : $convertedGrandTotal;
                    }
                @endphp
                <span class="rate-label">Total Package Cost for {{ $totalPax }} pax: {{ $currency }} {{ number_format(floor($displayGrandTotal), 0) }}</span>
            </div>
            @endif
        @endfor



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

                        $mealPlanText = '';
                        if ($room && $room->meal_plans && $room->meal_plans->count() > 0) {
                            $mealPlanNames = $room->meal_plans->map(function ($mp) {
                                $plan = Modules\Settings\Entities\MealPlan::find($mp->meal_plan_id);
                                return $plan ? $plan->name : '';
                            })->filter()->unique()->toArray();
                            $mealPlanText = ' with ' . implode(', ', $mealPlanNames);
                        }

                        $roomTypeName = optional($room?->room_type)->name ?? 'mentioned';
                        $location = optional($hotel?->sub_destination)->name ?? optional($hotel?->destination)->name ?? '';
                        $key = ($hotel->id ?? 0) . '_' . ($room->id ?? 0) . '_' . $location;
                        
                        if (!isset($mergedHotelsList[$key])) {
                            $mergedHotelsList[$key] = [
                                'room' => $roomTypeName,
                                'meals' => $mealPlanText,
                                'location' => $location,
                                'nights_dates' => [],
                            ];
                        }

                        $days = $hotelEnd->diffInDays($hotelStart);
                        for ($d = 0; $d < $days; $d++) {
                            $mergedHotelsList[$key]['nights_dates'][] = $hotelStart->copy()->addDays($d)->format('Y-m-d');
                        }
                    }

                    foreach ($mergedHotelsList as &$mhl) {
                        $mhl['nights'] = count(array_unique($mhl['nights_dates']));
                    }
                    unset($mhl);
                @endphp
                @foreach ($mergedHotelsList as $mhl)
                    @php $atLocation = $mhl['location'] ? " at " . $mhl['location'] : ""; @endphp
                    <li>✅ {{ $mhl['nights'] }} Night accommodation in the above mentioned hotel{{ $mhl['meals'] }}{{ $atLocation }}</li>
                @endforeach
                @php break; @endphp {{-- Only show first option's inclusions --}}
            @endforeach

            {{-- List transfers and activities day-wise — combined in schedule order (by entry id) --}}
            @foreach ($uniqueDates as $idx => $date)
                @php
                    $dayEntries = $itinerary->entries
                        ->where('date', $date)
                        ->whereIn('entry_type', ['TRANSFER', 'ACTIVITY'])
                        ->sortBy('start_time');
                    $hasItems = $dayEntries->count() > 0;
                @endphp
                @if($hasItems)
                    <li style="list-style:none; margin-left:-15px; margin-top:8px; margin-bottom:2px;">
                        <strong>Day {{ $idx + 1 }} ({{ date('d M', strtotime($date)) }})</strong>
                    </li>
                    @foreach ($dayEntries as $entry)
                        @if($entry->entry_type == 'TRANSFER')
                            @php
                                $transfer = Modules\Settings\Entities\Transfer::find($entry->subject_id);
                                $tName = optional($transfer)->vehicle_name ?? optional($transfer)->description ?? 'Transfer';
                                if (isset($entry->vehicle_count) && $entry->vehicle_count > 1) {
                                    $tName .= ' * ' . $entry->vehicle_count;
                                }
                            @endphp
                            <li>✅ {{ $tName }}</li>
                        @elseif($entry->entry_type == 'ACTIVITY')
                            @php
                                $activity = Modules\Settings\Entities\Activity::find($entry->subject_id);
                            @endphp
                            <li>✅ {{ optional($activity)->activity_name }}</li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <li>✅ English speaking customer service assistance</li>
        </ul>

        @if($opts['itinerary'])
        {{-- ============================================ --}}
        {{-- PROPOSED ITINERARY --}}
        {{-- ============================================ --}}
        <p class="section-header" style="font-size:12px; margin-top:20px;">Proposed Itinerary</p>

        <div class="itinerary-section">
            @foreach ($uniqueDates as $key => $date)
                @php
                    $dayEntries = $itinerary->entries->where('date', $date)->sortBy('start_time');
                    $dateFormatted = date('d M', strtotime($date));

                    // Build paired list: each item has a name and its own description
                    $dayItems = [];
                    foreach ($dayEntries as $item) {
                        $sub = null;
                        $itemName = '';
                        if ($item->entry_type == 'TRANSFER') {
                            $sub = Modules\Settings\Entities\Transfer::find($item->subject_id);
                            $itemName = optional($sub)->vehicle_name ?? optional($sub)->description ?? 'Transfer';
                            if (isset($item->vehicle_count) && $item->vehicle_count > 1) {
                                $itemName .= ' * ' . $item->vehicle_count;
                            }
                        } elseif ($item->entry_type == 'ACTIVITY') {
                            $sub = Modules\Settings\Entities\Activity::find($item->subject_id);
                            $itemName = trim(optional($sub)->activity_name ?? 'Activity');
                        }

                        if (!$itemName) continue;

                        // 1st priority: per-entry text editor description
                        // 2nd priority: subject's own description field
                        $entryDesc = '';
                        if (!empty($item->description)) {
                            $entryDesc = $item->description;
                        } elseif ($item->entry_type == 'ACTIVITY' && $sub && !empty($sub->description)) {
                            $entryDesc = $sub->description;
                        } elseif ($item->entry_type == 'TRANSFER' && $sub && !empty($sub->description)) {
                            $entryDesc = $sub->description;
                        }

                        $dayItems[] = ['name' => $itemName, 'desc' => $entryDesc];
                    }
                @endphp
                @if(count($dayItems) > 0)
                    <div style="margin-bottom: 10px;">
                        <span class="day-header">Day {{ $key + 1 }} ({{ $dateFormatted }}):</span>
                        <div style="margin-top: 3px; margin-left: 10px;">
                            @foreach($dayItems as $di)
                                <div style="margin-bottom: 2px;">✓ {{ $di['name'] }}</div>
                                @if(!empty($di['desc']))
                                    <div style="margin-bottom: 6px; margin-left: 12px; line-height: 1.7; text-align: justify; color: #555;">
                                        › {!! $di['desc'] !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @endif

        @if($opts['terms'])
        @php
            $packageTerm = Modules\Settings\Entities\PackageTerm::latest()->first();
        @endphp
        @if($packageTerm && !empty($packageTerm->package_terms))
            <p class="section-header" style="font-size:12px; margin-top:20px;">Package Terms &amp; Condition</p>
            <div class="package-terms-section" style="margin-top: 10px; line-height: 1.8;">
                {!! $packageTerm->package_terms !!}
            </div>
        @endif
        @endif

    </div>
</body>

</html>
