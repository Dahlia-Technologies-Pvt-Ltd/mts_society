<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Master\SocietyController;
use App\Http\Controllers\API\Master\MasterSubscriptionController;
use App\Http\Controllers\API\Master\MasterUserController;
use App\Http\Controllers\API\Master\SettingsController;
use App\Http\Controllers\API\Master\CountryStateController;
use App\Http\Controllers\API\Master\ChangePasswordController;
use App\Http\Controllers\API\Master\ForgotPasswordController;
use App\Http\Controllers\API\Master\ProfileUpdateController;
use App\Http\Controllers\API\Admin\TowerController;
use App\Http\Controllers\API\Admin\FloorController;
use App\Http\Controllers\API\RegisterController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
});
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//Register
Route::post('/register', [RegisterController::class, 'register']);
//forgot passeword api
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotpassword']);
//Masters
Route::post('/list-country', [CountryStateController::class, 'country']);
Route::post('/list-state', [CountryStateController::class, 'state']);
Route::post('/list-master-subscription', [MasterSubscriptionController::class, 'indexing']);

//Only For Super Admin
Route::middleware('auth:sanctum','superadmin')->group(function () {
    //master society apis
    Route::post('/add-society', [SocietyController::class, 'store']);
    Route::post('/list-society', [SocietyController::class, 'indexing']);
    Route::get('/show-society/{id}', [SocietyController::class, 'show']);
    Route::post('/delete-society', [SocietyController::class, 'delete']);
    //master subscription apis
    Route::post('/add-master-subscription', [MasterSubscriptionController::class, 'store']);
    Route::get('/show-master-subscription/{id}', [MasterSubscriptionController::class, 'show']);
    Route::post('/delete-master-subscription', [MasterSubscriptionController::class, 'delete']);
    //master user apis
    Route::post('/add-master-user', [MasterUserController::class, 'store']);
    Route::post('/list-master-user', [MasterUserController::class, 'indexing']);
    Route::get('/show-master-user/{id}', [MasterUserController::class, 'show']);
    Route::post('/delete-master-user', [MasterUserController::class, 'delete']);
    //system settings apis
    Route::post('/show-system-settings', [SettingsController::class, 'indexing']);
    Route::post('/edit-system-settings', [SettingsController::class, 'updating']);
    //User apis
    // MasterUserController
   
});
//Only For Admin
Route::middleware('auth:sanctum','admin','connect.society')->group(function () {
    //tower apis
    Route::post('/add-tower', [TowerController::class, 'store']);
    Route::post('/list-tower', [TowerController::class, 'indexing']);
    Route::get('/show-tower/{id}', [TowerController::class, 'show']);
    Route::post('/delete-tower', [TowerController::class, 'delete']);
    //floor apis
    Route::post('/add-floor', [FloorController::class, 'store']);
    Route::post('/list-floor', [FloorController::class, 'indexing']);
    Route::get('/show-floor/{id}', [FloorController::class, 'show']);
    Route::post('/delete-floor', [FloorController::class, 'delete']);
});
//Only For User
Route::middleware('auth:sanctum','user')->group(function () {

});
//For ALL
Route::middleware('auth:sanctum')->group(function () {
    //update profile(whole)
    Route::post('/update-user', [ProfileUpdateController::class, 'updateuser']);
    //update only profile picture
    Route::post('/update-profile-picture', [ProfileUpdateController::class, 'updateprofilepicture']);
    //change password api
    Route::post('/change-password', [ChangePasswordController::class, 'changepassword']);
    //forgot passeword api
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotpassword']);
    Route::post('/login-send-otp', [AuthController::class, 'loginsendotp']);
    //reset password api
    // Route::post('/reset-password', [ForgotPasswordController::class, 'ResetPassword']);
    

});