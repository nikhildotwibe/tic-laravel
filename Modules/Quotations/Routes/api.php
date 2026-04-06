<?php

use Illuminate\Http\Request;
use Modules\Quotations\Http\Controllers\ItineraryController;
use Modules\Quotations\Http\Controllers\QuotationsController;

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

Route::middleware('auth:api')->get('/quotations', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('itineraries', ItineraryController::class)->except('update');
    Route::post('itinerary-update/{id}', [ItineraryController::class, 'update']);
    // Route::get('itineraries/{id}/pricing', [ItineraryController::class, 'pricing']);
    Route::post('itineraries/{id}/set-pricing', [ItineraryController::class, 'setpricing']);
    Route::get('itineraries/{id}/pricing-history', [ItineraryController::class, 'pricingHistory']);
    Route::post('itineraries/{id}/restore-pricing/{snapshotId}', [ItineraryController::class, 'restorePricing']);
    Route::post('itinerary/print/{id}', [ItineraryController::class, 'print']);
    Route::post('itineraries/{id}/share-email', [ItineraryController::class, 'shareEmail']);
});
