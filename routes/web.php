<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// First, define the route to serve files from the storage directory
Route::get('storage/{path}', function ($path) {
    $filePath = storage_path("app/$path");

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');

// Then, define the wildcard route for your React application
Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');

Route::get('login', function() {
    return response()->json(['message' => 'Unauthorized.'], 401);
});