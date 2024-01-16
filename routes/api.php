<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Master\SocietyController;
use App\Http\Controllers\API\Master\MasterSubscriptionController;
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

Route::post('/add-master-subscription', [MasterSubscriptionController::class, 'store']);
Route::post('/list-master-subscription', [MasterSubscriptionController::class, 'indexing']);
Route::get('/show-master-subscription/{id}', [MasterSubscriptionController::class, 'show']);
Route::post('/delete-master-subscription', [MasterSubscriptionController::class, 'delete']); 


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
