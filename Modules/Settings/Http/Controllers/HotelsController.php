<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as ValidationValidator;
use Modules\Settings\Entities\Draft;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\RoomMealPlanEntry;
use Modules\Settings\Entities\RoomRateException;
use Modules\Settings\Transformers\HotelResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HotelsController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $query = Hotel::query();

            if ($request->sub_destination_id) {
                $query = $query->where('sub_destination_id', $request->sub_destination_id);
            }

            $hotels = $query->latest()->get();

            return $this->sendResponse(HotelResource::collection($hotels), 'All Hotel Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function requestValidator($requestData, string $id = null): ValidationValidator
    {
        $rules =
            [
                'draft_id' => 'nullable|exists:drafts,id,deleted_at,NULL',

                'name' => 'required|unique:hotels,name,'.$id.',id,deleted_at,NULL',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'sub_destination_id' => 'required|exists:sub_destinations,id,deleted_at,NULL',
                'place' => 'required|string',
                'category_id' => 'required|exists:categories,id,deleted_at,NULL',
                'property_type_id' => 'required|exists:property_types,id,deleted_at,NULL',
                'sales_email' => 'required|email',
                'contact_no' => 'nullable|unique:hotels,contact_no,'.$id.',id,deleted_at,NULL',
                'reservation_no' => 'nullable',
                'reservation_email' => 'nullable|email',
                'phone_number' => 'required',

                'rooms' => 'required|array',
                'rooms.*.market_type_id' => 'required|exists:market_types,id,deleted_at,NULL',
                'rooms.*.from_date' => 'required|date_format:Y-m-d',
                'rooms.*.to_date' => 'required|date_format:Y-m-d|after:rooms.*.from_date',
                'rooms.*.room_type_id' => 'required|exists:room_types,id,deleted_at,NULL',
                'rooms.*.single_bed_amount' => 'required|gte:0',
                'rooms.*.double_bed_amount' => 'required|gte:0',
                'rooms.*.is_triple_bed_available' => 'required|boolean',
                'rooms.*.triple_bed_amount' => 'required_if:rooms.*.is_triple_bed_available,1|gte:0',
                'rooms.*.is_quad_bed_available' => 'nullable|boolean',
                'rooms.*.quad_bed_amount' => 'required_if:rooms.*.is_quad_bed_available,1|gte:0',
                'rooms.*.two_bedroom_amount' => 'nullable|numeric|gte:0',
                'rooms.*.three_bedroom_amount' => 'nullable|numeric|gte:0',
                'rooms.*.four_bedroom_amount' => 'nullable|numeric|gte:0',
                'rooms.*.is_extra_bed_available' => 'required|boolean',
                'rooms.*.extra_bed_amount' => 'required_if:rooms.*.is_extra_bed_available,1|gte:0',
                'rooms.*.is_child_w_bed_available' => 'required|boolean',
                'rooms.*.child_w_bed_amount' => 'required_if:rooms.*.is_child_w_bed_available,1|gte:0',
                'rooms.*.is_child_n_bed_available' => 'required|boolean',
                'rooms.*.child_n_bed_amount' => 'required_if:rooms.*.is_child_n_bed_available,1|gte:0',
                'rooms.*.occupancy' => 'required|integer|min:0',
                'rooms.*.is_allotted' => 'required|boolean',
                'rooms.*.allotted_cut_off_days' => 'required_if:rooms.*.is_allotted,1|gte:0',

                // Exception date pricing
                'rooms.*.exceptions' => 'nullable|array',
                'rooms.*.exceptions.*.label' => 'nullable|string|max:100',
                'rooms.*.exceptions.*.dates' => 'required_with:rooms.*.exceptions.*|array',
                'rooms.*.exceptions.*.dates.*' => 'nullable|date_format:Y-m-d',
                'rooms.*.exceptions.*.single_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.double_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.triple_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.extra_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.child_w_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.child_n_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.quad_bed_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.two_bedroom_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.three_bedroom_amount' => 'nullable|numeric|gte:0',
                'rooms.*.exceptions.*.four_bedroom_amount' => 'nullable|numeric|gte:0',

                // 'rooms.*.images' => 'required|array|min:1',
                // 'rooms.*.images.*' => 'required|image|mimes:jpeg,jpg,png|max:2000',

                'rooms.*.meal_plans.*.id' => 'required|exists:meal_plans,id,deleted_at,NULL',
                'rooms.*.meal_plans.*.amount' => 'required|gt:0',
                'rooms.*.meal_plans.*.child_amount' => 'nullable|numeric|gte:0',

                'rooms.*.amenities.*' => 'required|exists:room_amenities,id,deleted_at,NULL',

                'amenities.*' => 'required|exists:hotel_amenities,id,deleted_at,NULL',

                'document_1' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
                'document_2' => 'array',
                'document_2.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
                'document_3' => 'array',
                'document_3.*' => 'nullable|mimes:doc,docx,txt,pdf|max:2000',
                'document_4' => 'array',
                'document_4.*' => 'nullable|mimes:doc,docx,txt,pdf|max:2000',
            ];

        $rules['rooms.*.images'] = 'nullable|array';
        $rules['rooms.*.images.*'] = 'nullable|image|mimes:jpeg,jpg,png|max:2000';

        return Validator::make($requestData, $rules)->setAttributeNames(
            [
                'rooms.*.market_type_id' => 'market type',
                'rooms.*.from_date' => 'from date',
                'rooms.*.to_date' => 'to date',
                'rooms.*.room_type_id' => 'room type',
                'rooms.*.single_bed_amount' => 'single bed amount',
                'rooms.*.double_bed_amount' => 'double bed amount',
                'rooms.*.is_triple_bed_available' => 'is triple bed available',
                'rooms.*.triple_bed_amount' => 'triple bed amount',
                'rooms.*.is_quad_bed_available' => 'is quad bed available',
                'rooms.*.quad_bed_amount' => 'quad bed amount',
                'rooms.*.two_bedroom_amount' => 'two bedroom amount',
                'rooms.*.three_bedroom_amount' => 'three bedroom amount',
                'rooms.*.four_bedroom_amount' => 'four bedroom amount',
                'rooms.*.is_extra_bed_available' => 'is extra bed available',
                'rooms.*.extra_bed_amount' => 'extra bed amount',
                'rooms.*.is_child_w_bed_available' => 'is child w bed available',
                'rooms.*.child_w_bed_amount' => 'child w bed amount',
                'rooms.*.is_child_n_bed_available' => 'is child n bed available',
                'rooms.*.child_n_bed_amount' => 'child n bed amount',
                'rooms.*.occupancy' => 'occupancy',
                'rooms.*.is_allotted' => 'is allotted',
                'rooms.*.allotted_cut_off_days' => 'allotted cut off days',

                'rooms.*.images' => 'room images',
                'rooms.*.images.*' => 'room image',

                'rooms.*.meal_plans.*.id' => 'meal plan id',
                'rooms.*.meal_plans.*.amount' => 'meal plan amount',
                'rooms.*.amenities.*' => 'room amenity',

                'amenities.*' => 'hotel amenity',

                'document_2.*' => 'file',
                'document_3.*' => 'file',
                'document_4.*' => 'file',
            ]
        );
    }

    public function process($requestData, string $id = null)
    {
        // data spliting up
        $roomData = $requestData['rooms'];
        $hotelAmenitiesData = $requestData['amenities'] ?? [];
        $document1 = $requestData['document_1'] ?? [];
        $document2 = $requestData['document_2'] ?? [];
        $document3 = $requestData['document_3'] ?? [];
        $document4 = $requestData['document_4'] ?? [];

        unset(
            $requestData['rooms'],
            $requestData['amenities'],
            $requestData['document_1'],
            $requestData['document_2'],
            $requestData['document_3'],
            $requestData['document_4'],
        );
        $hotelData = $requestData;

        // create or update hotel
        $hotel = Hotel::updateOrcreate(['id' => $id], $hotelData);

        // document 1
        if (! empty($document1)) {
            $hotel->addMediaFromRequest('document_1')->toMediaCollection('hotel-profile-images');
        }

        // document 2
        foreach ($document2 as $key => $media) {
            $hotel->addMedia($media)->toMediaCollection('hotel-images');
        }

        // document 3
        foreach ($document3 as $key => $media) {
            $hotel->addMedia($media)->toMediaCollection('hotel-documents-3');
        }

        // document 4
        foreach ($document4 as $key => $media) {
            $hotel->addMedia($media)->toMediaCollection('hotel-documents-4');
        }

        // sync hotel amenities
        $hotelAmenities = [];
        foreach ($hotelAmenitiesData as $key => $amenity) {
            $hotelAmenities[$key] = [
                'hotel_amenity_id' => $amenity,
                'id' => Str::uuid()->toString(),
            ];
        }
        $hotel->amenities()->sync($hotelAmenities);

        // dd($roomData);

        // create or update rooms in the hotel

        // dd($roomData);
        $savedObjects = [];

        foreach ($roomData as $key => $room) {
            $mealPlansData = isset($room['meal_plans']) ? $room['meal_plans'] : [];
            $amenitiesData = isset($room['amenities']) ? $room['amenities'] : [];
            $imagesData = $room['images'] ?? null;
            
            $hasExceptionsKey = array_key_exists('has_exceptions', $room);
            $exceptionsData = $room['exceptions'] ?? [];
            file_put_contents(storage_path('logs/exceptions_debug.txt'),
                json_encode([
                    'room_key'          => $key,
                    'has_exceptions_key'=> $hasExceptionsKey,
                    'has_exceptions_val'=> $room['has_exceptions'] ?? 'NOT_SET',
                    'exceptions_count'  => count($exceptionsData),
                    'exceptions_data'   => $exceptionsData,
                ]) . PHP_EOL, FILE_APPEND);
            
            unset($room['meal_plans'], $room['amenities'], $room['images'], $room['exceptions'], $room['has_exceptions']);

            // These flags don't have DB columns — unset them to prevent SQL errors
            unset($room['is_two_bedroom_available'], $room['is_three_bedroom_available'], $room['is_four_bedroom_available']);

            // Convert empty-string bedroom amounts to null so the DB stores NULL
            // (frontend sends "" when bedroom type is disabled, a value when enabled)
            foreach (['two_bedroom_amount', 'three_bedroom_amount', 'four_bedroom_amount'] as $bedroomField) {
                if (array_key_exists($bedroomField, $room) && ($room[$bedroomField] === '' || $room[$bedroomField] === null)) {
                    $room[$bedroomField] = null;
                }
            }

            $room['hotel_id'] = $hotel->id;
            file_put_contents(storage_path('logs/debug_room.txt'), json_encode($room).PHP_EOL, FILE_APPEND);
            // $room = Room::updateOrcreate(['id' => $room['id'] ?? null], $room);
            // $room = $this->updateOrCreate(new Room(), [$room], 'hotel_id', $hotel->id, true)[0];
            $savedObjects[] = $room = Room::updateOrCreate(['id' => $room['id'] ?? null], $room);

            // store room images
            if (! empty($imagesData)) {
                foreach ($imagesData as $key => $media) {
                    $room->addMedia($media)->toMediaCollection('room-images');
                }
            }

            // sync meal plans
            $mealPlans = [];
            $room->meal_plans()->delete();
            foreach ($mealPlansData as $key => $meal) {
                // $mealPlans[$key] = [
                //     'meal_plan_id' => $meal['id'],
                //     'amount' => $meal['amount'],
                //     'id' => Str::uuid()->toString(),
                // ];
                $mealPlan = new RoomMealPlanEntry;
                $mealPlan->room_id = $room->id;
                $mealPlan->meal_plan_id = $meal['id'];
                $mealPlan->amount = $meal['amount'];
                $mealPlan->child_amount = $meal['child_amount'] ?? 0;
                $mealPlan->save();
            }
            // $room->meal_plans()->saveMany($mealPlans);

            // sync amenities
            $amenities = [];
            foreach ($amenitiesData as $key => $amenity) {
                $amenities[$key] = [
                    'room_amenity_id' => $amenity,
                    'id' => Str::uuid()->toString(),
                ];
            }
            $room->amenities()->sync($amenities);

            // sync rate exceptions
            if ($hasExceptionsKey) {
                $room->rate_exceptions()->delete();
                foreach ($exceptionsData as $exc) {
                    $dates = $exc['dates'] ?? [];
                    $label = $exc['label'] ?? null;
                    foreach ($dates as $date) {
                        if (empty($date)) continue;
                        RoomRateException::create([
                            'room_id'             => $room->id,
                            'exception_date'      => $date,
                            'label'               => $label,
                            'single_bed_amount'   => isset($exc['single_bed_amount'])    && $exc['single_bed_amount']    !== '' ? $exc['single_bed_amount']    : null,
                            'double_bed_amount'   => isset($exc['double_bed_amount'])    && $exc['double_bed_amount']    !== '' ? $exc['double_bed_amount']    : null,
                            'triple_bed_amount'   => isset($exc['triple_bed_amount'])    && $exc['triple_bed_amount']    !== '' ? $exc['triple_bed_amount']    : null,
                            'extra_bed_amount'    => isset($exc['extra_bed_amount'])     && $exc['extra_bed_amount']     !== '' ? $exc['extra_bed_amount']     : null,
                            'child_w_bed_amount'  => isset($exc['child_w_bed_amount'])   && $exc['child_w_bed_amount']   !== '' ? $exc['child_w_bed_amount']   : null,
                            'child_n_bed_amount'  => isset($exc['child_n_bed_amount'])   && $exc['child_n_bed_amount']   !== '' ? $exc['child_n_bed_amount']   : null,
                            'quad_bed_amount'     => isset($exc['quad_bed_amount'])      && $exc['quad_bed_amount']      !== '' ? $exc['quad_bed_amount']      : null,
                            'two_bedroom_amount'  => isset($exc['two_bedroom_amount'])   && $exc['two_bedroom_amount']   !== '' ? $exc['two_bedroom_amount']   : null,
                            'three_bedroom_amount'=> isset($exc['three_bedroom_amount']) && $exc['three_bedroom_amount'] !== '' ? $exc['three_bedroom_amount'] : null,
                            'four_bedroom_amount' => isset($exc['four_bedroom_amount'])  && $exc['four_bedroom_amount']  !== '' ? $exc['four_bedroom_amount']  : null,
                        ]);
                    }
                }
            }
        }

        Room::where('hotel_id', $id)->whereNotIn('id', collect($savedObjects)->pluck('id'))->delete();

        // draft discard
        $draft = Draft::find($requestData['draft_id'] ?? null);
        if ($draft) {
            $draft->delete();
        }

        DB::commit();

            $hotel = Hotel::with('rooms.media', 'rooms.meal_plans', 'rooms.amenities', 'rooms.rate_exceptions')->find($hotel->id);

        return $hotel;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all())->validate();
            $hotel = $this->process($request->all());

            return $this->sendResponse(HotelResource::make($hotel), 'Hotel created Successfully', 201);
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->HandleException($exception);
        }
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
            $hotel = Hotel::with('rooms.media', 'rooms.meal_plans', 'rooms.amenities', 'rooms.rate_exceptions')->findOrFail($id);

            return $this->sendResponse(HotelResource::make($hotel), 'Hotel Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->requestValidator($request->all(), $id)->validate();
            $hotel = $this->process($request->all(), $id);

            return $this->sendResponse(HotelResource::make($hotel), 'Hotel Updated', 200);
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
        try {
            Hotel::findOrFail($id)->delete();

            return $this->sendResponse([], 'Hotel Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function deleteImage($id)
    {
        try {
            $media = Media::findOrFail($id);

            $media->delete();

            return $this->sendResponse([], 'Image deleted successfully', 200);

        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
