<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\Role;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\User\Entities\UsersRole;
use Modules\User\Transformers\RoleShowResource;

class RolesController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $query = Role::query()->latest();

            $query = $query->where('slug', '!=', 'admin');

            if (request()->has('name')) {
                $query = $query->where('name', 'LIKE', '%' . request()->name . '%');
            }

            $data = $query->get();

            return $this->sendResponse(
                RoleShowResource::collection($data),
                'Roles Retrieved Successfully',
                200
            );
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
                'name' => 'required|unique:roles,name',
                'description' => 'nullable|max:300',
                'is_active' => 'nullable|boolean',
                'permissions.*' => 'required|exists:permissions,id|distinct',
            ])->validate();

            $role = new Role();
            $role->name = $request->input('name');
            $role->slug = Str::slug($request->input('name'), '_');
            $role->description = $request->input('description');
            $role->is_active = $request->input('is_active', 1);
            $role->save();


            $rolePermissions = [];
            if (!empty($request->input('permissions'))) {
                foreach ($request->input('permissions') as $permission) {
                    $rolePermissions[] = ['permission_id' => $permission, 'id' => Str::uuid()->toString()];
                }
            }

            $role->permissions()->sync($rolePermissions);

            return $this->sendResponse(RoleShowResource::make($role), 'Role Saved Successfully', 201);
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
        try {
            $role = Role::findOrFail($id);
            return $this->sendResponse(RoleshowResource::make($role), 'Role fetched Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            Validator::make($request->all(), [
                'name' => 'required|unique:roles,name,' . $id . ',id,deleted_at,NULL',
                'slug' => '',
                'description' => 'required|max:300',
                'is_active' => 'required|boolean',
                'sync' => 'required|boolean',
                'permissions.*' => 'required|exists:permissions,id|distinct',
            ])->validate();

            $role = Role::findOrFail($id);


            $role->name = $request->input('name');
            $role->slug = Str::slug($request->input('name'), '_');
            $role->description = $request->input('description');
            $role->is_active = $request->input('is_active');
            $role->update();

            $rolePermissions = [];
            if (!empty($request->input('permissions'))) {
                foreach ($request->input('permissions') as $permission) {
                    $rolePermissions[] = ['permission_id' => $permission, 'id' => Str::uuid()->toString()];
                }
            }

            $role->permissions()->sync($rolePermissions);


            if ((bool) $request->input('sync')) {
                $users = $role->users;
                $userPermissions = [];
                foreach ($users as $user) {
                    foreach ($request->input('permissions') as $permission) {
                        $userPermissions[$user->seq][] = ['permission_id' => $permission, 'id' => Str::uuid()->toString()];
                    }
                }

                if ($userPermissions) {
                    foreach ($users as $user) {
                        $user->permissions()->sync($userPermissions[$user->seq]);
                    }
                }
            }

            return $this->sendResponse(RoleshowResource::make($role), 'Role Updated Successfully', 200);
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
            $count = UsersRole::where('role_id', $id)->count();
            if (!$count) {
                Role::findOrFail($id)->delete();
            } else {
                throw ValidationException::withMessages(['This role has ' . $count . ' active users in it, delete aborted ']);
            }

            return $this->sendResponse('success', 'Role Deleted Successfully', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
