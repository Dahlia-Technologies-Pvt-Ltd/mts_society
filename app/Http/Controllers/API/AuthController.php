<?php

namespace App\Http\Controllers\API;

use App\Models\Master\{MasterUser, UserOtp, Country, State};
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\JsonResponse;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use App\Helpers\MailHelper;
use Laravel\Sanctum\PersonalAccessTokenFactory;

class AuthController extends ResponseController
{
    public function __construct()
    {
    }

    public function login(Request $request): JsonResponse
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required',
                'password' => 'required_without:otp', // Password is required unless OTP is provided
                'otp' => 'required_without:password', // OTP is required unless Password is provided
            ],
            [
                'email.required' => 'Email is required.',
                'password.required_without' => 'Password or OTP is required.',
                'otp.required_without' => 'Password or OTP is required.',
            ]
        );

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $credentialsUserCode = array(
                'username' => $request->email, 'password' => $request->password, 'status' => 0,
            );
            $loginID = false; // Default false to send the response accordingly in the end
            // Attempt to authorize using username
            if (Auth::attemptWhen($credentialsUserCode)) {
                $loginID = true;
            } else {
                if (isset($request->password)) {
                    if (is_numeric($request->email)) {
                        // Array to verify credentials using phone number
                        $credentialsEmail = array(
                            'phone_number' => $request->email, 'password' => $request->password, 'status' => 0,
                        );
                    } else {
                        // Array to verify credentials using email
                        $credentialsEmail = array(
                            'email' => $request->email, 'password' => $request->password, 'status' => 0,
                        );
                    }
                    //Attempt to authorize using email
                    if (Auth::attemptWhen($credentialsEmail)) {
                        $loginID = true;
                    }
                } else {
                    $otp_params['otp'] = $request->otp;
                    $otp_params['keyword'] = $request->email;
                    $user_otp_obj = new UserOtp();
                    $opt_user_id = $user_otp_obj->verifyOtp($otp_params);
                    if($opt_user_id > 0){
                        if (Auth::loginUsingId($opt_user_id)) {
                            $loginID = true;
                        }
                    } else {
                        $response['status'] = 400;
                        $response['message'] = 'Invalid otp or the time has expired';
                        return $this->sendError($response);
                    }
                }
            }

            if ($loginID == false) {
                $response['status'] = 401;
                $response['message'] = 'Invalid Email or Phone Number.';
                return $this->sendFailedLoginResponse($request);
            } else {

                $user = Auth::user();
                $user['token'] = $user->createToken('MTSSOCIETY')->plainTextToken;
                $response['status'] = 200;
                $response['message'] = 'User authenticated successfully.';
                $response['data'] = $user->only(['id', 'username', 'name', 'user_code', 'email', 'usertype', 'phone_number', 'token', 'profile_picture']);
                return $this->sendResponse($response);
            }
        }
    }

    public function loginsendotp(Request $request): JsonResponse
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required'
            ],
            [
                'email.required' => 'Email field is required.'
            ]
        );
        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            // Load user from database 
            $user = MasterUser::getEmailSingle($request->email);


            if (empty($user)) {
                $response['status'] = 401;
                $response['message'] = 'Email does not exists.';
                return $this->sendError($response);
            } else {
                $id = $user->id;
                $obj = new UserOtp();
                $params['id'] = $id;
                $params['name'] = $user->name;
                $params['email'] = $user->email;
                $sendOtp = $obj->sendotp($params);
                if ($sendOtp['status'] == 200) {
                    return $this->sendResponse($sendOtp);
                } else {
                    return $this->sendError($sendOtp);
                }
            }
        }
    }
    public function logout()
    {
        if (Auth::check()) {
            $user = auth()->user();
            if ($user) {
                $user->tokens->each(function ($token) {
                    $token->delete();
                });
            }
            $response['status'] = 200;
            $response['message'] = 'Successfully logged out';
            return $this->sendResponse($response);
        } else {
            $response['status'] = 401;
            $response['message'] = 'You are not logged in.';
            return $this->sendResponse($response);
        }
    }
}
