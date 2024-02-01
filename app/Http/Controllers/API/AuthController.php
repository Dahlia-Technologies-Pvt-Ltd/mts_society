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
                    $otp_params['opt'] = $request->otp;
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
                $response['data'] = $user->only(['id', 'username', 'name', 'user_code', 'email', 'usertype', 'phone_number', 'token', 'profile_picture','master_society_ids','societies']);
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

    public function registrationotpverify(Request $request): JsonResponse
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'email' => 'required', // Password is required unless OTP is provided
                'otp' => 'required|integer', // OTP is required unless Password is provided
            ],
            [
                'email.required' => 'Email is required.',
                'otp.required' => 'OTP is required.',
            ]
        );
        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $otp_params['opt'] = $request->otp;
            $otp_params['keyword'] = $request->email;
            $user_otp_obj = new UserOtp();
            $opt_user_id = $user_otp_obj->verifyOtp($otp_params);
            if($opt_user_id > 0){
                    $user_status = MasterUser::find($opt_user_id)->first();
                    $user_status->status = 2;//waiting for approval
                    $user_status->save();

                    $response['status'] = 200;
                    $response['message'] = 'User registered successfully.';
                    $response['data'] = $user_status->only(['id', 'username', 'name', 'user_code', 'email', 'usertype','status', 'phone_number', 'token', 'profile_picture']);               
                    return $this->sendResponse($response);

            } else {
                $response['status'] = 400;
                $response['message'] = 'Invalid otp or the time has expired';
                return $this->sendError($response);
            }

            //=========================================
            /*$data_query = UserOtp::join('master_users', 'master_users.id', '=', 'user_otps.master_user_id')->where('otp', $request->otp)->where('expire_at', '>=', Carbon::now());
            $keyword = $request->email;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('master_users.phone_number', '=', $keyword)
                    ->orWhere('master_users.email', '=', $keyword);
            });
            if ($data_query->exists()) {
                $data_query->select([
                    'master_users.id', 'user_otps.id AS otp_id','master_users.name AS name'
                ]);
                $user_otp_data = $data_query->first();
                    $user_status = MasterUser::where('email', $request->email)->first();
                    $user_status->status = 2;
                    $user_status->save();
                    $loginID = true;
            } else {
                $response['status'] = 400;
                $response['message'] = 'Invalid otp or the time has expired';
                return $this->sendError($response);
            }
            if ($loginID == false) {
                $response['status'] = 401;
                $response['message'] = 'Invalid Email or Phone Number.';
                return $this->sendFailedLoginResponse($request);
            } else {
                $response['status'] = 200;
                $response['message'] = 'User registered successfully.';
                $response['data'] = $user_status->only(['id', 'username', 'name', 'user_code', 'email', 'usertype','status', 'phone_number', 'token', 'profile_picture']);
                $user_otp = UserOtp::find($user_otp_data->otp_id);
                $user_otp->otp = 0;
                $user_otp->expire_at = Carbon::now()->subMinutes(5);
                $user_otp->save();
                return $this->sendResponse($response);
            }*/
        }
    }
    // 'name' => 'required|string|max:255',
    // 'email' => 'required|email|unique:master_users,email',
    // 'phone_number' => 'required|phone_number|unique:master_users,phone_number',
    // 'country_id' => 'required|integer|min:1',
    // 'state_id' => 'required|integer|min:1',
    // 'city' => 'required|string'
    public function residentregistration(Request $request): JsonResponse
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|unique:master_users',
                'phone_number' => 'required|unique:master_users',
                'country_id' => 'required|integer|min:1',
                'state_id' => 'required|integer|min:1',
                'city' => 'required|string'
            ],
            [
                'email.required' => 'Email is required and should be unique.',
                // 'phone_number.required' => 'Phone number is required and should be unique.',
                'country_id.required' => 'Country is required.',
                'state_id.required' => 'State is required.'
            ]
        );
        // $validator = Validator::make($request->all(), [
        //     'tower_name'                                    => 'required|unique:towers,tower_name,' . $id . ',id,deleted_at,NULL|max:255',
        // ]);
        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            if ($request->country_id > 0) {
                $existingRecord = Country::find($request->country_id);
                if (!$existingRecord) {
                    $response['status'] = 400;
                    $response['message'] = 'Record not found for the provided country ID.';
                    return $this->sendError($response);
                }
            }
            if ($request->state_id > 0) {
                $existingRecord = State::find($request->state_id);
                if (!$existingRecord) {
                    $response['status'] = 400;
                    $response['message'] = 'Record not found for the provided state ID.';
                    return $this->sendError($response);
                }
            }
            $obj = new MasterUser();
            $user = MasterUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_name' => isset($request->user_name) ? $request->user_name : 'User',
                'phone_number' => $request->phone_number,
                'password' => Hash::make('password'),
                'usertype' => 0,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city' => $request->city,
                'user_code' => $obj->generateUserCode(),
                'status' => 1
            ]);

            // }
            $obj2 = new UserOtp();
            $id = $user->id;
            $params['id'] = $id;
            $params['name'] = $user->name;
            $params['email'] = $user->email;
            $sendOtp = $obj2->sendotp($params);
            if ($sendOtp['status'] == 200) {
                return $this->sendResponse($sendOtp);
            } else {
                return $this->sendError($sendOtp);
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
