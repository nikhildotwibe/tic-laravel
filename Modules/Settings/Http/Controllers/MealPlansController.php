<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\MarketType;
use Modules\Settings\Entities\MealPlan;

class MealPlansController extends BaseController
{

    public function index()
    {
        try {
            $mealPlans = MealPlan::latest()->get();
            return $this->sendResponse($mealPlans, 'All Meal Plans Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:meal_plans,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $MealPlan = new MealPlan();
            $MealPlan->name = $request->name;
            $MealPlan->save();

            return $this->sendResponse($MealPlan, 'Meal Plan created Successfully', 201);
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
            $MealPlan = MealPlan::findOrFail($id);
            return $this->sendResponse($MealPlan, 'Meal Plan Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $MealPlan = MealPlan::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:meal_plans,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $MealPlan->name = $request->name;
            $MealPlan->update();

            return $this->sendResponse($MealPlan, 'Meal Plan Updated', 200);
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
            MealPlan::findOrFail($id)->delete();
            return $this->sendResponse([], 'Meal Plan Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
