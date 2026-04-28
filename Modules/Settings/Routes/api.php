<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\ActivitiesController;
use Modules\Settings\Http\Controllers\ActivityTypeController;
use Modules\Settings\Http\Controllers\VehicleTypeController;
use Modules\Settings\Http\Controllers\AgentsController;
use Modules\Settings\Http\Controllers\CategoriesController;
use Modules\Settings\Http\Controllers\CountriesController;
use Modules\Settings\Http\Controllers\CurrencyController;
use Modules\Settings\Http\Controllers\CustomerController;
use Modules\Settings\Http\Controllers\DestinationsController;
use Modules\Settings\Http\Controllers\DraftsController;
use Modules\Settings\Http\Controllers\EnquiriesController;
use Modules\Settings\Http\Controllers\HotelAmenitiesController;
use Modules\Settings\Http\Controllers\HotelsController;
use Modules\Settings\Http\Controllers\LeadSourceController;
use Modules\Settings\Http\Controllers\MarketTypesController;
use Modules\Settings\Http\Controllers\MealPlansController;
use Modules\Settings\Http\Controllers\PriorityController;
use Modules\Settings\Http\Controllers\PropertyTypesController;
use Modules\Settings\Http\Controllers\RequirementsController;
use Modules\Settings\Http\Controllers\RoomAmenitiesController;
use Modules\Settings\Http\Controllers\RoomTypesController;
use Modules\Settings\Http\Controllers\SubDestinationsController;
use Modules\Settings\Http\Controllers\SupplierController;
use Modules\Settings\Http\Controllers\SystemSettingsController;
use Modules\Settings\Http\Controllers\TaxController;
use Modules\Settings\Http\Controllers\TransfersController;

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

Route::prefix('settings')->middleware('auth:sanctum')->group(function () {
    Route::resource('countries', CountriesController::class);
    Route::resource('languages', LanguagesController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('system-settings', SystemSettingsController::class);
    Route::resource('destinations', DestinationsController::class);
    Route::resource('sub-destinations', SubDestinationsController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('property-types', PropertyTypesController::class);
    Route::resource('market-types', MarketTypesController::class);
    Route::resource('room-types', RoomTypesController::class);
    Route::resource('room-amenities', RoomAmenitiesController::class);
    Route::resource('hotel-amenities', HotelAmenitiesController::class);
    Route::resource('meal-plans', MealPlansController::class);
    Route::post('store-draft', [DraftsController::class, 'store']);
    Route::get('fetch-draft', [DraftsController::class, 'index']);
    Route::resource('agents', AgentsController::class);
    Route::resource('hotels', HotelsController::class)->except('update');

    Route::delete('hotel-image/{id}', [HotelsController::class, 'deleteImage']);


    Route::post('hotel-update/{id}', [HotelsController::class, 'update']);
    Route::apiResource('transfers', TransfersController::class)->except('update');
    Route::post('transfer-update/{id}', [TransfersController::class, 'update']);
    Route::patch('transfer-status-update/{id}', [TransfersController::class, 'updateStatus']);
    Route::apiResource('activities', ActivitiesController::class);
    Route::patch('activity-status-update/{id}', [ActivitiesController::class, 'updateStatus']);
    Route::apiResource('lead-sources', LeadSourceController::class);
    Route::apiResource('priorities', PriorityController::class);
    Route::apiResource('currencies', CurrencyController::class);
    Route::apiResource('requirements', RequirementsController::class);
    Route::apiResource('enquiries', EnquiriesController::class);
    Route::apiResource('activity-types', ActivityTypeController::class);
    Route::apiResource('vehicle-types', VehicleTypeController::class);
    Route::get('suppliers-search-by-mobile', [SupplierController::class, 'searchByMobile']);

    // Tax Settings (GST)
    Route::get('tax-settings', [TaxController::class, 'index']);
    Route::post('tax-settings', [TaxController::class, 'store']);

    // Additional / Custom Taxes
    Route::get('additional-taxes', [TaxController::class, 'indexAdditional']);
    Route::post('additional-taxes', [TaxController::class, 'storeAdditional']);
    Route::get('additional-taxes/{id}', [TaxController::class, 'showAdditional']);
    Route::put('additional-taxes/{id}', [TaxController::class, 'updateAdditional']);
    Route::delete('additional-taxes/{id}', [TaxController::class, 'destroyAdditional']);
});
