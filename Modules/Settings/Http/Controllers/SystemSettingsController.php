<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\SystemSetting;

class SystemSettingsController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Jsonresponse
     */
    public function index()
    {
        try {
            $settings = SystemSetting::latest()->get();
            return $this->sendResponse($settings, 'All System Settings Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'key' => 'required|unique:system_settings,key,NULL,id,deleted_at,NULL',
                'value' => 'required',
            ])->validate();

            $setting = new SystemSetting();
            $setting->key = $request->key;
            $setting->value = $request->value;
            $setting->save();

            return $this->sendResponse($setting, 'System Setting created Successfully', 201);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('settings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('settings::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
