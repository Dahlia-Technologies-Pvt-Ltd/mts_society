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
use App\Http\Controllers\API\Master\EmailController;
use App\Http\Controllers\API\Admin\TowerController;
use App\Http\Controllers\API\Admin\ApprovalController;
use App\Http\Controllers\API\Admin\FloorController;
use App\Http\Controllers\API\Admin\MasterServiceProviderController;
use App\Http\Controllers\API\Admin\FlatController;
use App\Http\Controllers\API\Admin\WingsController;
use App\Http\Controllers\API\Admin\ParkingController;
use App\Http\Controllers\API\Admin\ResidentialUserDetailController;
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
//resident register
Route::post('/register-resident', [RegisterController::class, 'residentregistration']);

//verify resident register
Route::post('/verify-resident', [RegisterController::class, 'registrationotpverify']);
//forgot passeword api
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotpassword']);
//login send otp api
Route::post('/login-send-otp', [AuthController::class, 'loginsendotp']);
 
//reset password api
Route::post('/reset-password', [ForgotPasswordController::class, 'ResetPassword']);
//Masters
Route::post('/list-country', [CountryStateController::class, 'country']);
Route::post('/list-state', [CountryStateController::class, 'state']);
Route::post('/list-master-subscription', [MasterSubscriptionController::class, 'index']);

//Only For Super Admin
Route::middleware('auth:sanctum','superadmin')->group(function () {
    //master society apis
    Route::post('/add-society', [SocietyController::class, 'store']);
    Route::post('/list-society', [SocietyController::class, 'index']);
    Route::get('/show-society/{id}', [SocietyController::class, 'show']);
    Route::post('/delete-society', [SocietyController::class, 'destroy']);
    //master subscription apis
    Route::post('/add-master-subscription', [MasterSubscriptionController::class, 'store']);
    Route::get('/show-master-subscription/{id}', [MasterSubscriptionController::class, 'show']);
    Route::post('/delete-master-subscription', [MasterSubscriptionController::class, 'destroy']);
    //master user apis
    Route::post('/add-master-user', [MasterUserController::class, 'store']);
    Route::post('/list-master-user', [MasterUserController::class, 'index']);
    Route::get('/show-master-user/{id}', [MasterUserController::class, 'show']);
    Route::post('/delete-master-user', [MasterUserController::class, 'destroy']);
    //system settings apis
    Route::post('/show-system-settings', [SettingsController::class, 'index']);
    Route::post('/edit-system-settings', [SettingsController::class, 'updating']);
    //email template
    Route::post('/add-emailtemplate', [EmailController::class, 'store']);
    Route::post('/delete-emailtemplate', [EmailController::class, 'destroy']);
    Route::post('/list-emailtemplate', [EmailController::class, 'index']);
    Route::get('/show-emailtemplate/{id}', [EmailController::class, 'show']);
    //User apis
    // MasterUserController
   
});
//Only For Admin
Route::middleware('auth:sanctum','admin','connect.society')->group(function () {
    
    Route::post('/list-user-for-approval', [ApprovalController::class, 'index']);
    Route::post('/user-for-approval', [ApprovalController::class, 'approval']);
    //tower apis
    Route::post('/add-tower', [TowerController::class, 'store']);
    Route::post('/list-tower', [TowerController::class, 'index']);
    Route::get('/show-tower/{id}', [TowerController::class, 'show']);
    Route::post('/delete-tower', [TowerController::class, 'destroy']);
    //floor apis
    Route::post('/add-floor', [FloorController::class, 'store']);
    Route::post('/list-floor', [FloorController::class, 'index']);
    Route::get('/show-floor/{id}', [FloorController::class, 'show']);
    Route::post('/delete-floor', [FloorController::class, 'destroy']);
     //flat apis
     Route::post('/add-flat', [FlatController::class, 'store']);
     Route::post('/list-flat', [FlatController::class, 'index']);
     Route::get('/show-flat/{id}', [FlatController::class, 'show']);
     Route::post('/delete-flat', [FlatController::class, 'destroy']);
     //wings apis
     Route::post('/edit-wing', [WingsController::class, 'edit']);
     Route::post('/delete-wing', [WingsController::class, 'destroy']);
     //parking api
     Route::post('/add-parking', [ParkingController::class, 'store']);
     Route::post('/list-parking', [ParkingController::class, 'index']);
     Route::get('/show-parking/{id}', [ParkingController::class, 'show']);
     Route::post('/delete-parking', [ParkingController::class, 'destroy']);
      //service provider api
      Route::post('/add-service-provider', [MasterServiceProviderController::class, 'store']);
      Route::post('/list-service-provider', [MasterServiceProviderController::class, 'index']);
      Route::get('/show-service-provider/{id}', [MasterServiceProviderController::class, 'show']);
      Route::post('/delete-service-provider', [MasterServiceProviderController::class, 'destroy']);
      //Residential user apis
      Route::post('/add-residential-user', [ResidentialUserDetailController::class, 'store']);
      Route::post('/list-residential-user', [ResidentialUserDetailController::class, 'index']);
      Route::get('/show-residential-user/{id}', [ResidentialUserDetailController::class, 'show']);
      Route::post('/delete-residential-user', [ResidentialUserDetailController::class, 'destroy']);
     
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

    Route::get('/get-profile', [ProfileUpdateController::class, 'show']);
    Route::get('/get-parking-type', [ParkingController::class, 'parkingVehicleType']);
    
    
    
    

});