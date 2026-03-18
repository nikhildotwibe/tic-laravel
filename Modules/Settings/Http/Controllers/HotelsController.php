<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator as ValidationValidator;
use Modules\Settings\Entities\Draft;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\HotelAmenity;
use Modules\Settings\Entities\MealPlan;
use Modules\Settings\Entities\Room;
use Modules\Settings\Entities\RoomAmenity;
use Modules\Settings\Entities\RoomMealPlanEntry;
use Modules\Settings\Transformers\HotelResource;

class HotelsController extends BaseController
{

    public function index(Request $request)
    {
        try {
            $query = Hotel::query();

            if($request->sub_destination_id){
                $query = $query->where('sub_destination_id',$sub_destination_id);
            }

            $hotels = $query->latest()->get();
            return $this->sendResponse(HotelResource::collection($hotels), 'All Hotel Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function requestValidator($requestData, string|null $id = null): ValidationValidator
    {
        $rules =
            [
                'draft_id' => 'nullable|exists:drafts,id,deleted_at,NULL',

                'name' => 'required|unique:hotels,name,' . $id . ',id,deleted_at,NULL',
                'destination_id' => 'required|exists:destinations,id,deleted_at,NULL',
                'sub_destination_id' => 'required|exists:sub_destinations,id,deleted_at,NULL',
                'place' => 'required|string',
                'category_id' => 'required|exists:categories,id,deleted_at,NULL',
                'property_type_id' => 'required|exists:property_types,id,deleted_at,NULL',
                'sales_email' => 'required|email',
                'contact_no' => 'nullable|unique:hotels,contact_no,' . $id . ',id,deleted_at,NULL',
                'reservation_no' => 'nullable|unique:hotels,reservation_no,' . $id . ',id,deleted_at,NULL',
                'reservation_email' => 'nullable|email|unique:hotels,reservation_email,' . $id . ',id,deleted_at,NULL',
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
                'rooms.*.is_extra_bed_available' => 'required|boolean',
                'rooms.*.extra_bed_amount' => 'required_if:rooms.*.is_extra_bed_available,1|gte:0',
                'rooms.*.is_child_w_bed_available' => 'required|boolean',
                'rooms.*.child_w_bed_amount' => 'required_if:rooms.*.is_child_w_bed_available,1|gte:0',
                'rooms.*.is_child_n_bed_available' => 'required|boolean',
                'rooms.*.child_n_bed_amount' => 'required_if:rooms.*.is_child_n_bed_available,1|gte:0',
                'rooms.*.occupancy' => 'required|integer|min:0',
                'rooms.*.is_allotted' => 'required|boolean',
                'rooms.*.allotted_cut_off_days' => 'required_if:rooms.*.is_allotted,1|gte:0',

                // 'rooms.*.images' => 'required|array|min:1',
                // 'rooms.*.images.*' => 'required|image|mimes:jpeg,jpg,png|max:2000',

                'rooms.*.meal_plans.*.id' => 'required|exists:meal_plans,id,deleted_at,NULL',
                'rooms.*.meal_plans.*.amount' => 'required|gt:0',

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

        if ($id != null) {
            $rules['rooms.*.images'] = 'array|min:1';
            $rules['rooms.*.images.*'] = 'image|mimes:jpeg,jpg,png|max:2000';
        } else {
            $rules['rooms.*.images'] = 'required|array|min:1';
            $rules['rooms.*.images.*'] = 'required|image|mimes:jpeg,jpg,png|max:2000';
        }


        return Validator::make($requestData, $rules)->setAttributeNames(
            [
                'rooms.*.market_type_id' => 'market type',
                'rooms.*.from_date' => 'from date',
                'rooms.*.to_date' => 'to date',
                'rooms.*.room_type_id' => 'room type',
                'rooms.*.single_bed_amount' => 'single bed amount',
                'rooms.*.double_bed_amount' => 'double bed amount',
                'rooms.*.is_triple_bed_available' => 'is triple bed available',
                'rooms.*.triple_bed_amount' => 'is triple bed available',
                'rooms.*.is_extra_bed_available' => 'is extra bed available',
                'rooms.*.extra_bed_amount' => 'is extra bed available',
                'rooms.*.is_child_w_bed_available' => 'is child w bed available',
                'rooms.*.child_w_bed_amount' => 'is child w bed available',
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

    public function process($requestData, string|null $id = null)
    {
        // data spliting up
        $roomData = $requestData['rooms'];
        $hotelAmenitiesData = $requestData['amenities'];
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
        if (!empty($document1)) {
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
            unset($room['meal_plans'], $room['amenities'], $room['images']);

            $room['hotel_id'] = $hotel->id;
            // $room = Room::updateOrcreate(['id' => $room['id'] ?? null], $room);
            // $room = $this->updateOrCreate(new Room(), [$room], 'hotel_id', $hotel->id, true)[0];
            $savedObjects[] = $room = Room::updateOrCreate(['id' => $room['id'] ?? null], $room);

            // store room images
            if (!empty($imagesData)) {
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
                $mealPlan->room_id =  $room->id;
                $mealPlan->meal_plan_id =  $meal['id'];
                $mealPlan->amount =  $meal['amount'];
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
        }

        Room::where('hotel_id', $id)->whereNotIn('id', collect($savedObjects)->pluck('id'))->delete();

        // draft discard
        $draft = Draft::find($requestData['draft_id'] ?? null);
        if ($draft) {
            $draft->delete();
        }

        DB::commit();

        $hotel = Hotel::with('rooms.media')->find($hotel->id);
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
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $hotel = Hotel::with('rooms.media')->findOrFail($id);
            return $this->sendResponse(HotelResource::make($hotel), 'Hotel Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $this->requestValidator($request->all(), $id)->validate();
            $hotel = $this->process($request->all(), $id);
            return $this->sendResponse(HotelResource::make($hotel), 'Hotel Updated', 200);
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
            Hotel::findOrFail($id)->delete();
            return $this->sendResponse([], 'Hotel Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
public function deleteImage($id)
{
    try {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('id', $id)
            ->where('collection_name', 'hotel-images')
            ->firstOrFail();

        $media->delete();

        return $this->sendResponse([], 'Image deleted successfully', 200);
    } catch (Exception $e) {
        return $this->HandleException($e);
    }
}
}
