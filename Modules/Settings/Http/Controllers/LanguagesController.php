<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Settings\Entities\languages;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Language;

class LanguagesController extends BaseController
{

    public function index()
    {

        try {
            $languages = Language::latest()->get();
            return $this->sendResponse($languages, 'All Languages Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'language' => 'required|unique:languages,language,NULL,id,deleted_at,NULL',
                'slug' => 'required|unique:languages,slug,NULL,id,deleted_at,NULL',
            ])->validate();

            $language = new Language();
            $language->language = $request->language;
            $language->slug = $request->slug;
            $language->save();

            return $this->sendResponse($language, 'Language created Successfully', 201);
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
        // return view('settings::show');
        try {
            $language = Language::findOrFail($id);
            return $this->sendResponse($language, 'language Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $language = Language::findOrFail($id);

            Validator::make($request->all(), [
                'language' => 'required|unique:languages,language,' . $id . ',id,deleted_at,NULL',
                'slug' => 'required|unique:languages,slug,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $language->language = $request->language;
            $language->slug = $request->slug;
            $language->update();

            return $this->sendResponse($language, 'Language Updated', 200);
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
            Language::findOrFail($id)->delete();
            return $this->sendResponse([], 'Language Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
