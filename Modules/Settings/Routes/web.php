<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;

Route::get('/uploads/{path}', function ($path) {
    $fullPath = public_path('uploads/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');
