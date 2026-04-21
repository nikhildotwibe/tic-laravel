<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\ModulesController;
use Modules\User\Http\Controllers\PermissionsController;
use Modules\User\Http\Controllers\RolesController;
use Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


    Route::prefix('user')->as("user.")->group(function () {
        Route::post('register', [UserController::class, 'store']);
        Route::post('login', [UserController::class, 'login'])->name('login');
        Route::get('roles-list', [RolesController::class, 'index']);
        Route::put('update/{id}', [UserController::class, 'update']);
    });

Route::prefix('user')->as("user.")->middleware('auth:sanctum')->group(function () {
    Route::get('info', [UserController::class, 'info']);
    Route::get('show/{id}', [UserController::class, 'show']);
    Route::get('list', [UserController::class, 'index']);
    Route::get('delete/{id}', [UserController::class, 'destroy']);
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('change-password');
});

Route::prefix('module')->as("module.")->middleware('auth:sanctum')->group(function () {
    Route::get('list', [ModulesController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('roles', RolesController::class);
    Route::get('permissions', [PermissionsController::class, 'index']);
});
