<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\User as User;
use Modules\User\Transformers\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Transformers\PermissionResource;
use Modules\User\Transformers\UserShowResource;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $query = User::query()->latest();
            if (request()->has('name')) {
                $query = $query->where('name', 'LIKE', '%' . request()->name . '%');
            }

            $data = $query->paginate(pageLength());

            return $this->sendResponse(
                $this->paginatedResourceCollection(UserResource::class, $data),
                'Users Retrieved Successfully',
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
            DB::beginTransaction();
            Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users',
                'dob' => 'nullable|date_format:Y-m-d',
                'gender' => 'nullable|in:male,female,other',
                'role_id' => 'exists:roles,id',
                'profile_picture' => 'nullable|mimes:jpg,png,jpeg|max:20480',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'language' => 'required|exists:languages,id,deleted_at,NULL',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'country_id' => 'nullable|exists:countries,id,deleted_at,NULL',
            ])->setAttributeNames([
                'c_password' => 'Confirm Password',
                'country_id' => 'country'
            ])->validate();

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->password = bcrypt($request->password);
            $user->address = $request->address;
            $user->language = $request->language;
            $user->start_date = $request->start_date;
            $user->end_date = $request->end_date;
            $user->country = $request->country_id;
            $user->save();

            $roleID = $request->input('role_id');

            if (!empty($roleID)) {
                $user->roles()->sync([$roleID => ['id' => Str::uuid()->toString()]]);
            }

            if ($request->hasFile('profile_picture')) {
                $user->clearMediaCollection(MEDIA_PROFILE_IMAGES);

                $user->addMediaFromRequest('profile_picture')
                    ->usingFileName('profile_pic_' . Carbon::now()->timestamp)
                    ->toMediaCollection(MEDIA_PROFILE_IMAGES);
            }
            DB::commit();

            return $this->sendResponse(UserResource::make($user), 'User created', 201);
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
            $data = User::findOrFail($id);

            return $this->sendResponse(
                UserShowResource::make($data),
                'User Retrieved Successfully',
                200
            );
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        return view('user::edit');
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

            DB::beginTransaction();

            $user = User::findOrFail($id);

            Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|unique:users,username,' . $id . ',id,deleted_at,NULL',
                'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'phone' => 'required|unique:users,phone,' . $id . ',id,deleted_at,NULL',
                'dob' => 'nullable|date_format:Y-m-d',
                'gender' => 'nullable|in:male,female,other',
                'role_id' => 'exists:roles,id',
                'profile_picture' => 'nullable|mimes:jpg,png,jpeg|max:20480',
                'password' => 'nullable',
                'c_password' => 'required_if:password,!=,null|same:password',
                'language' => 'required|exists:languages,id,deleted_at,NULL',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'country_id' => 'nullable|exists:countries,id,deleted_at,NULL',
            ])->setAttributeNames([
                'c_password' => 'Confirm Password',
                'country_id' => 'country'
            ])->validate();

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->dob = $request->dob;
            $user->gender = $request->gender;
            $user->address = $request->address;
            $user->language = $request->language;
            $user->start_date = $request->start_date;
            $user->end_date = $request->end_date;
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->country = $request->country_id;
            $user->save();

            $roleID = $request->input('role_id');

            if (!empty($roleID)) {
                $user->roles()->sync([$roleID => ['id' => Str::uuid()->toString()]]);
            }

            if ($request->hasFile('profile_picture')) {
                $user->clearMediaCollection(MEDIA_PROFILE_IMAGES);

                $user->addMediaFromRequest('profile_picture')
                    ->usingFileName('profile_pic_' . Carbon::now()->timestamp)
                    ->toMediaCollection(MEDIA_PROFILE_IMAGES);
            }
            DB::commit();

            return $this->sendResponse(UserResource::make($user), 'User Updated', 200);
        } catch (Exception $exception) {
            DB::rollBack();
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
            $user = User::findOrFail($id);
            $user->delete();
            return $this->sendResponse([], 'User deleted successfully.', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function login(Request $request): JsonResponse
    {
        try {

            Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ])->validate();

            if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                $user = Auth::user();
                $user->token = $user->createToken('APP')->plainTextToken;
                $user->username = $user->username;
                $user->is_super_admin = $user->roles->pluck('name')->contains('Super Admin');
                $user->permissions = PermissionResource::collection($user->permissions);
                return $this->sendResponse($user, 'User Logged in.');
            } else {
                return $this->sendError('Unauthorized.', ['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();
            return $this->sendResponse([], 'User logged out successfully.');
        } catch (\Exception $exception) {
            return $this->HandleException($exception);
        }
    }


    public function info(): JsonResponse
    {
        try {
            return $this->sendResponse(UserShowResource::make(Auth::user()), 'User Info fetched.', 200);
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'new_password' => 'required',
                'c_password' => 'required|same:new_password',
            ])->setAttributeNames([
                'c_password' => 'Confirm Password'
            ])->validate();

            $roles = Auth::user()->roles;
            if (!isset($roles[0])) {
                return $this->sendError('error.', ['error' => 'Logged in user has no valid roles set, so this operation can not be perfomed. '], 401);
            } else {
                if ($roles[0]->name == 'Super Admin') {
                    Validator::make($request->all(), [
                        'username' => 'required',
                    ])->validate();

                    $user = User::where('username', $request->username)->first();
                } else {
                    Validator::make($request->all(), [
                        'current_password' => 'required',
                    ])->validate();

                    if (Hash::check($request->current_password, Auth::user()->password)) {
                        $user = User::findOrFail(Auth::user()->id);
                    } else {
                        return $this->sendError('error.', ['current_password' => 'Incorrect Password'], 401);
                    }
                }
                if ($user) {
                    $user->password = bcrypt($request->new_password);
                    $user->save();
                    return $this->sendResponse(UserResource::make($user), 'Password changed successfully.', 200);
                } else {
                    return $this->sendError('error.', ['username' => 'No user found with given credentials'], 401);
                }
            }
        } catch (Exception $exception) {
            return $this->HandleException($exception);
        }
    }
}
