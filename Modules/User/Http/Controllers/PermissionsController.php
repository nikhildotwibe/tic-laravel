<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Module;
use Modules\User\Transformers\ModuleResource;
use Illuminate\Support\Str;
use Modules\User\Entities\Permission;
use Modules\User\Transformers\ModuleShowResource;

class PermissionsController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        try {
            $query = Module::query()->latest();
            $data = $query->get();


            return $this->sendResponse(
                ModuleShowResource::collection($data),
                'Permissions Retrieved Successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
