<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/storage-link', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($targetFolder, $linkFolder);
});

Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'Migration ran successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Migration failed',
            'error' => $e->getMessage()
        ]);
    }
});

// Seed all databases
Route::get('/db:seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'All database seeds ran successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Seeding failed',
            'error' => $e->getMessage()
        ]);
    }
});

Route::get('/db-seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'All database seeds ran successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Seeding failed',
            'error' => $e->getMessage()
        ]);
    }
});

// Seed only countries
Route::get('/db-seed/countries', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Modules\Settings\Database\Seeders\CountriesTableSeeder',
            '--force' => true
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Countries seeded successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Countries seeding failed',
            'error' => $e->getMessage()
        ]);
    }
});

