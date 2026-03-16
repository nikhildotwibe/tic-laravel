<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Settings\Entities\Category;

class CategoriesController extends BaseController
{

    public function index()
    {

        try {
            $categories = Category::latest()->get();
            return $this->sendResponse($categories, 'All Categories Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function store(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:categories,name,NULL,id,deleted_at,NULL',
            ])->validate();

            $category = new Category();
            $category->name = $request->name;
            $category->save();

            return $this->sendResponse($category, 'Category created Successfully', 201);
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
        // return view('settings::show');
        try {
            $category = Category::findOrFail($id);
            return $this->sendResponse($category, 'Category Fetched', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $category = Category::findOrFail($id);

            Validator::make($request->all(), [
                'name' => 'required|unique:categories,name,' . $id . ',id,deleted_at,NULL',
            ])->validate();


            $category->name = $request->name;
            $category->update();

            return $this->sendResponse($category, 'Category Updated', 200);
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
            Category::findOrFail($id)->delete();
            return $this->sendResponse([], 'Category Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
