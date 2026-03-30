<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        a {
            background-color: transparent
        }

        [hidden] {
            display: none
        }

        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }

        *,
        :after,
        :before {
            box-sizing: border-box;
            border: 0 solid #e2e8f0
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        svg,
        video {
            display: block;
            vertical-align: middle
        }

        video {
            max-width: 100%;
            height: auto
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity))
        }

        .bg-gray-100 {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity))
        }

        .border-gray-200 {
            --tw-border-opacity: 1;
            border-color: rgb(229 231 235 / var(--tw-border-opacity))
        }

        .border-t {
            border-top-width: 1px
        }

        .flex {
            display: flex
        }

        .grid {
            display: grid
        }

        .hidden {
            display: none
        }

        .items-center {
            align-items: center
        }

        .justify-center {
            justify-content: center
        }

        .font-semibold {
            font-weight: 600
        }

        .h-5 {
            height: 1.25rem
        }

        .h-8 {
            height: 2rem
        }

        .h-16 {
            height: 4rem
        }

        .text-sm {
            font-size: .875rem
        }

        .text-lg {
            font-size: 1.125rem
        }

        .leading-7 {
            line-height: 1.75rem
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto
        }

        .ml-1 {
            margin-left: .25rem
        }

        .mt-2 {
            margin-top: .5rem
        }

        .mr-2 {
            margin-right: .5rem
        }

        .ml-2 {
            margin-left: .5rem
        }

        .mt-4 {
            margin-top: 1rem
        }

        .ml-4 {
            margin-left: 1rem
        }

        .mt-8 {
            margin-top: 2rem
        }

        .ml-12 {
            margin-left: 3rem
        }

        .-mt-px {
            margin-top: -1px
        }

        .max-w-6xl {
            max-width: 72rem
        }

        .min-h-screen {
            min-height: 100vh
        }

        .overflow-hidden {
            overflow: hidden
        }

        .p-6 {
            padding: 1.5rem
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem
        }

        .pt-8 {
            padding-top: 2rem
        }

        .fixed {
            position: fixed
        }

        .relative {
            position: relative
        }

        .top-0 {
            top: 0
        }

        .right-0 {
            right: 0
        }

        .shadow {
            --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1);
            --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
        }

        .text-center {
            text-align: center
        }

        .text-gray-200 {
            --tw-text-opacity: 1;
            color: rgb(229 231 235 / var(--tw-text-opacity))
        }

        .text-gray-300 {
            --tw-text-opacity: 1;
            color: rgb(209 213 219 / var(--tw-text-opacity))
        }

        .text-gray-400 {
            --tw-text-opacity: 1;
            color: rgb(156 163 175 / var(--tw-text-opacity))
        }

        .text-gray-500 {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity))
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity))
        }

        .text-gray-700 {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity))
        }

        .text-gray-900 {
            --tw-text-opacity: 1;
            color: rgb(17 24 39 / var(--tw-text-opacity))
        }

        .underline {
            text-decoration: underline
        }

        .antialiased {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

        .w-5 {
            width: 1.25rem
        }

        .w-8 {
            width: 2rem
        }

        .w-auto {
            width: auto
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr))
        }

        @media (min-width:640px) {
            .sm\:rounded-lg {
                border-radius: .5rem
            }

            .sm\:block {
                display: block
            }

            .sm\:items-center {
                align-items: center
            }

            .sm\:justify-start {
                justify-content: flex-start
            }

            .sm\:justify-between {
                justify-content: space-between
            }

            .sm\:h-20 {
                height: 5rem
            }

            .sm\:ml-0 {
                margin-left: 0
            }

            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem
            }

            .sm\:pt-0 {
                padding-top: 0
            }

            .sm\:text-left {
                text-align: left
            }

            .sm\:text-right {
                text-align: right
            }
        }

        @media (min-width:768px) {
            .md\:border-t-0 {
                border-top-width: 0
            }

            .md\:border-l {
                border-left-width: 1px
            }

            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media (min-width:1024px) {
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem
            }
        }

        @media (prefers-color-scheme:dark) {
            .dark\:bg-gray-800 {
                --tw-bg-opacity: 1;
                background-color: rgb(31 41 55 / var(--tw-bg-opacity))
            }

            .dark\:bg-gray-900 {
                --tw-bg-opacity: 1;
                background-color: rgb(17 24 39 / var(--tw-bg-opacity))
            }

            .dark\:border-gray-700 {
                --tw-border-opacity: 1;
                border-color: rgb(55 65 81 / var(--tw-border-opacity))
            }

            .dark\:text-white {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity))
            }

            .dark\:text-gray-400 {
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity))
            }

            .dark\:text-gray-500 {
                --tw-text-opacity: 1;
                color: rgb(107 114 128 / var(--tw-text-opacity))
            }
        }
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            font-size: 11px;
        }

        .border-table {
            border-collapse: collapse;
            width: 100%;
        }

        .border-table th,
        .border-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        table th,
        table td {
            padding: 8px;
            text-align: left;
        }

        .text-justify {
            text-align: justify;
        }
    </style>
</head>

<body class="antialiased">


    <table style="width: 100%;">


        <tr>
            <td colspan="2" style="float: right; text-align: left;line-height:25px">
                <span>
                    Dear Sir1<br>
                    Greetings from TIC Tours...!!!<br>
                    <span class="text-justify">
                        Thanks for deciding to avail services from tic tours, a leading travel and holidays management
                        Company. We Hereby forward you the complete package tour plan with all details, for further
                        clarification, or Change As per your idea or planning please do call or mail us.
                    </span>
                </span>
            </td>
        </tr>

        @php
            $start = Carbon\Carbon::parse($itinerary->enquiry->start_date);
            $end = Carbon\Carbon::parse($itinerary->enquiry->end_date);
            $daysCount = $end->diffInDays($start);
            $count = $daysCount - 1 . ' N | ' . $daysCount . ' D ';
        @endphp

        <tr>
            <td colspan="2" style="float: right; text-align: left;line-height:25px;">
                <span style="background-color:#d9ead3;padding:15% !important">
                    {{$count . $itinerary->package_name }}
                </span>
            </td>
        </tr>

        <tr>
            <td style="float: right; text-align: left;line-height:25px;">
                No. of Guests :
            </td>
            <td>
                {{ $itinerary->adult_count . ' Adult +' . $itinerary->child_count . ' Child' }}
            </td>
        </tr>
        <tr>
            <td style="float: right; text-align: left;line-height:25px;">
                Traveling Date :
            </td>
            <td>
                {{ date('D, d M, Y', strtotime($itinerary->start_date)) }}
            </td>
        </tr>
    </table>

    <table class="border-table" style="width: 100%;margin-top:8px;margin-bottom:10px">
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="4" style="background-color: black;color:white">Query Details</td>
        </tr>
        <tr>
            <td width="20%">Query ID:</td>
            <td width="40%">#{{ $itinerary->seq }}</td>
            <td width="20%">Adult(s):</td>
            <td width="20%">{{ $itinerary->adult_count }}</td>
        </tr>
        <tr>
            <td>Nights:</td>



            <td>{{ $count }}</td>
            <td>Child(s):</td>
            <td>{{ $itinerary->child_count }}</td>
        </tr>
        <tr>
            <td>Destination Covered:</td>
            <td>{{ $itinerary->destination->name }}</td>
            <td>Start Date:</td>
            <td>{{ date('D, d M, Y', strtotime($itinerary->start_date)) }}</td>
        </tr>
        <tr>
            <td>Query Date:</td>
            <td>{{ date('D, d M, Y', strtotime($itinerary->created_at)) }}</td>
            <td>End Date</td>
            <td>{{ date('D, d M, Y', strtotime($itinerary->end_date)) }}</td>
            {{-- <td>Tue, 05 Sep, 2023</td> --}}
        </tr>
    </table>


    <table class="border-table" style="width: 100%;margin-top:10px;">
        <tbody>

            <tr style="background-color: #E4E4E4;color:white;border:1px solid #E4E4E4 !important">
                <td width="35%">Destinations</td>
                @foreach ($itinerary->entries->where('entry_type', 'HOTEL') as $item)
                    <td>Hotel / Resort <br>{{ $item->option }}</td>
                @endforeach

            </tr>



            <tr>
                <td>{{ $itinerary->package_name }}</td>

                @php
                    $perPersonAmounts = [];
                @endphp
                @foreach ($itinerary->entries->where('entry_type', 'HOTEL') as $key => $item)
                    @php

                        $perPersonAmounts[$item->id] = [];
                        $hotel = Modules\Settings\Entities\Hotel::find($item->subject_id);


                        $adultCount = $itinerary?->enquiry?->adult_count ?? 0;
                        $childCount = $itinerary?->enquiry?->child_count ?? 0;
                        $adultPerPersonAmount = 0;
                        $adulltNetAmount = 0;

                        $chilPerPersonAmount = 0;
                        $childNetAmount = 0;


                        $adulltPerPersonNetAmount = 0;
                        $childWPerPersonNetAmount = 0;
                        $childNPerPersonNetAmount = 0;
                        $childWPerPersonAmount = 0;
                        $childNPerPersonAmount = 0;

                        foreach ($itinerary->entries as $key => $entry) {

                            if ($entry->entry_type == 'HOTEL') {
                                $room = Modules\Settings\Entities\Room::find($entry->room_id);
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
                                $activityStartDate = $entry['start_date'];
                                $activityEndDate = $entry['end_date'];
                                $activityEstimation = Modules\Settings\Entities\ActivityEstimation::where('activity_id', $entry['subject_id'])->whereDate('from_date', '<=', $activityStartDate)->whereDate('to_date', '>=', $activityEndDate)->first();
                                $adulltPerPersonNetAmount += $activityEstimation->adult_cost;
                                $childWPerPersonNetAmount += $activityEstimation->child_cost;
                                $childNPerPersonNetAmount += $activityEstimation->child_cost;
                            }


                        }

                        if ($adultCount != 0) {
                            $adultPerPersonAmount = $adulltPerPersonNetAmount / $adultCount;
                        }

                        if ($childCount != 0) {
                            $childWPerPersonAmount = $childWPerPersonNetAmount / $childCount;
                            $childNPerPersonAmount = $childNPerPersonNetAmount / $childCount;
                        }

                        $perPersonAmounts[$item->id] = [
                            'adult' => $adultPerPersonAmount,
                            'child_w' => $childWPerPersonAmount,
                            'child_n' => $childNPerPersonAmount
                        ];


                    @endphp

                    <td>{{ $hotel->name ?? "N/A" }}</td>
                @endforeach

                @php
                    info($perPersonAmounts);
                @endphp


            </tr>



            <tr>
                <td>Rate Per Person on Double Sharing Basis</td>
                @foreach ($itinerary->entries->where('entry_type', 'HOTEL') as $key => $item)
                    <td>{{ $perPersonAmounts[$item->id]['adult'] }}</td>
                @endforeach
            </tr>


            <tr>
                <td>Rate Per Child with Extra Bed</td>
                @foreach ($itinerary->entries->where('entry_type', 'HOTEL') as $key => $item)
                    <td>{{ $perPersonAmounts[$item->id]['child_w'] }}</td>
                @endforeach
            </tr>

            <tr>
                <td>Rate Per Child without Bed</td>
                @foreach ($itinerary->entries->where('entry_type', 'HOTEL') as $key => $item)
                    <td>{{ $perPersonAmounts[$item->id]['child_n'] }}</td>
                @endforeach
            </tr>




        </tbody>
    </table>







    <table class="" style="width: 100%;margin-top:8px;margin-bottom:10px">
        <tr>
            <td colspan="2" style="background-color: black;color:white">Itinerary Details</td>
        </tr>

        @php
            $uniqueDates = array_unique($itinerary->entries->pluck('date')->toArray());
            sort($uniqueDates);
        @endphp

        @foreach ($uniqueDates as $key => $date)
            <tr style="width: 100%;margin-top:8px;margin-bottom:10px">
                <td width="20%" style="background-color: #E2E2E2">
                    {{ date('d M Y', strtotime($date)) }}
                </td>
                <td width="80%" style="background-color: #F5F5F5">
                    Day {{ $key + 1 }}<br>
                    @foreach ($itinerary->entries->where('date', $date) as $k => $item)
                        @php
                            if ($item->entry_type == 'HOTEL') {
                                info($item->room->meal_plans);

                                $mealPlansArray = $item->room->meal_plans->toArray();

                                $sub = Modules\Settings\Entities\Hotel::find($item->subject_id);
                                if (!empty($mealPlansArray)) {
                                    $mealPlanNames = array_map(function ($mealPlanEntry) {
                                        info($mealPlanEntry);
                                        $p = Modules\Settings\Entities\MealPlan::find($mealPlanEntry['meal_plan_id']);
                                        return $p->name;
                                    }, $mealPlansArray);

                                    info($mealPlanNames);

                                    $mealPlans = implode(',', $mealPlanNames);
                                    echo 'Accomodation in ' . optional($sub)->name . ',(' . optional($item->room->room_type)->name . ') with (Meal(' . $mealPlans . ').';
                                } else {
                                    echo 'Accomodation in ' . optional($sub)->name . ',(' . optional($item->room->room_type)->name . ').';
                                }
                            } elseif ($item->entry_type == 'TRANSFER') {
                                $sub = Modules\Settings\Entities\Transfer::find($item->subject_id);
                                echo 'Transfer From (' . optional($sub)->vehicle_name . ')';
                            } elseif ($item->entry_type == 'ACTIVITY') {
                                $sub = Modules\Settings\Entities\Activity::find($item->subject_id);
                                echo optional($sub)->activity_name;
                            }
                        @endphp
                        <br>
                    @endforeach
                </td>
            </tr>
        @endforeach

    </table>

    {{-- <table class="" style="width: 100%;margin-top:8px;margin-bottom:10px">
        <tr>
            <td width="20%" style="background-color: #E2E2E2">01 Sep 2023</td>
            <td width="80%" style="background-color: #F5F5F5">Day 3</td>
        </tr>
    </table>

    <table class="" style="width: 100%;margin-top:8px;margin-bottom:10px">
        <tr>
            <td width="20%" style="background-color: #E2E2E2">01 Sep 2023</td>
            <td width="80%" style="background-color: #F5F5F5">Day 4</td>
        </tr>
    </table> --}}

    <span>

        IMPORTANT NOTE: <br>
        • THE ABOVE PACKAGE IS ONLY AN OFFER AND NOT A CONFIRMATION. WE SHALL PROCEED WITH YOUR BOOKING ONLY AFTER WE
        RECEIVE YOUR CONFIRMATION.<br>
        • THE AIRFARE QUOTED, IF ANY, IS AS OF NOW AND IS SUBJECT TO CHANGE.<br>
        • IN CASE OF NON-AVAILABILITY OF ROOMS AT THE HOTELS MENTIONED, WE SHALL PROVIDE YOU ALTERNATE HOTELS OF SIMILAR
        CATEGORY.<br>
        • CHECK-IN/CHECK-OUT TIME AT THE HOTEL IS 12.00 HRS.<br>
        • BOOKING CONFIRMATION IS SUBJECT TO AVAILABILITY.<br>
        • THE ABOVE RATES ARE VALID FOR THE MENTIONED PERIOD ONLY.<br>
        • 100% PACKAGE COST SHOULD BE PAID 03 DAYS PRIOR TO DEPARTURE OR MENTIONED CUT OFF DATE<br>
        • TIC TOURS RESERVES THE RIGHT TO CHANGE/MODIFY OR TERMINATE THE OFFER ANY TIME AT ITS OWN DISCRETION AND
        WITHOUT ANY PRIOR NOTICE.<br>
        CANCELLATION POLICY<br>
        CANCELLATION CHARGES PER PERSON WILL BE APPLICABLE AS FOLLOWS:<br>
        • IF CANCELLATION IS MADE ANY TIME NOT LESS THAN 16 DAYS PRIOR TO DEPARTURE, 20-30% OF PACKAGE COST SHALL BE
        DEDUCTED.<br>
        • IF CANCELLATION IS MADE 15 TO 07 DAYS PRIOR TO DEPARTURE, 50-60% OF TOUR COST SHALL BE DEDUCTED.<br>
        • IF CANCELLATION IS MADE 06 TO 03 DAY PRIOR TO DEPARTURE, 75-85% OF TOUR COST SHALL BE DEDUCTED.<br>
        • IN CASE PASSENGER IS NO SHOW AT THE TIME OF DEPARTURE, 100% OF TOUR COST SHALL BE DEDUCTED.<br>
        NOTE 2:<br>
        1. FOR INDIAN PAYMENT 5% GST IS APPLICABLE AND ROE WILL BE XE.COM +1.5 ON THE DAY OF DEPOSIT<br>
        2. EVERY SWIFT TRANSACTION ADD USD 35 AS BANK CHARGES. OUTWARD REMITTANCE CHARGE TO BE BORNE BY TRANSFEROR<br>
        3. IF ARRIVAL/DEPARTURE AT DMK AIRPORT PLEASE INFORM US BEFORE QUOTING.<br>
        4. SIC TRANSFERS NOT AVAILABLE BETWEEN 6 PM AND 8 AM<br>
        5. SIC TRANSFERS AVAILABLE FROM SUVARNABHUMI AIRPORT ONLY<br>
        6. CONFIRMED TOUR VOUCHER WILL BE ISSUED 3 DAYS BEFORE TRAVELING DATE<br>
    </span>

</body>

</html>