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
            $requiredModules = ['Dashboard', 'Leads', 'Enquiry', 'Reports', 'Settings'];
            $permissionKeys = [
                ['name' => 'All', 'slug' => '-read-all'],
                ['name' => 'Added', 'slug' => '-read-added'],
                ['name' => 'Assigned', 'slug' => '-read-assigned'],
                ['name' => 'Added and Assigned', 'slug' => '-read-added-and-assigned'],
                ['name' => 'All', 'slug' => '-write-all'],
                ['name' => 'Added', 'slug' => '-write-added'],
                ['name' => 'Assigned', 'slug' => '-write-assigned'],
                ['name' => 'Added and Assigned', 'slug' => '-write-added-and-assigned'],
                ['name' => 'All', 'slug' => '-update-all'],
                ['name' => 'Added', 'slug' => '-update-added'],
                ['name' => 'Assigned', 'slug' => '-update-assigned'],
                ['name' => 'Added and Assigned', 'slug' => '-update-added-and-assigned'],
                ['name' => 'All', 'slug' => '-delete-all'],
                ['name' => 'Added', 'slug' => '-delete-added'],
                ['name' => 'Assigned', 'slug' => '-delete-assigned'],
                ['name' => 'Added and Assigned', 'slug' => '-delete-added-and-assigned'],
            ];

            foreach ($requiredModules as $modName) {
                $module = Module::firstOrCreate(
                    ['name' => $modName],
                    ['id' => Str::uuid()->toString()]
                );

                foreach ($permissionKeys as $permKey) {
                    $slug = Str::lower($modName) . $permKey['slug'];
                    Permission::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'id' => Str::uuid()->toString(),
                            'name' => $permKey['name'],
                            'module_id' => $module->id,
                        ]
                    );
                }
            }

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
