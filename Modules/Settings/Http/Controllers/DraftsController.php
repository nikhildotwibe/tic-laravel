<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Agent;
use Modules\Settings\Entities\Country;
use Modules\Settings\Entities\Draft;
use Modules\Settings\Entities\Hotel;
use Modules\Settings\Entities\HotelAmenity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DraftsController extends BaseController
{

    public function index()
    {
        try {

            $query = Draft::query()->latest();
            if (request()->has('type')) {
                $query = $query->where('type', $this->getModelClass(request()->type));
            }
            $draft = $query->all();

            return $this->sendResponse($draft, 'Draft Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'type' => 'required|in:agent,hotel', // Todo : will be added more
                'id' => 'nullable',
                'properties' => 'required|json'
            ])->validate();


            $draft = new Draft();
            $draft->subject_type = $this->getModelClass($request->type);
            $draft->subject_id = $request->id;
            $draft->properties = $request->properties;
            $draft->save();

            return $this->sendResponse($draft, 'Draft created Successfully', 201);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    private function getModelClass(string $type)
    {
        return match ($type) {
            'agent' => Agent::class,
            'hotel' => Hotel::class,
            default =>
            throw new NotFoundHttpException('Invalid Type'),
        };
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            Draft::findOrFail($id)->delete();
            return $this->sendResponse([], 'Draft discarded Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
