<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Master\SocietyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/add-society', [SocietyController::class, 'store']);
Route::post('/list-society', [SocietyController::class, 'indexing']);
Route::get('/show-society/{id}', [SocietyController::class, 'show']);
Route::post('/delete-society', [SocietyController::class, 'delete']); 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
