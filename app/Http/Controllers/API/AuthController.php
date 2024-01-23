<?php

namespace App\Http\Controllers\API;

use App\Models\Master\{MasterUser, UserOtp};
use Illuminate\Http\Request;
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
                    // 
                    $data_query = UserOtp::join('master_users', 'master_users.id', '=', 'user_otps.master_user_id')->where('otp', $request->otp);
                    $keyword = $request->email;
                    $data_query->where(function ($query) use ($keyword) {
                        $query->where('master_users.phone_number', '=', $keyword)
                            ->orWhere('master_users.email', '=', $keyword);
                    });
                    $data_query->select([
                        'master_users.phone_number'
                    ]);

                    if ($data_query->exists()) {
                        $phone_number = $data_query->first()->toArray()['phone_number'];
                        $credentialsEmail = array(
                            'phone_number' => $phone_number, 'status' => 0
                        );

                    } else {
                        $loginID = false;
                        $response['status'] = 404;
                        $response['message'] = 'Invalid Email or Phone Number.';
                    }
                    if (Auth::once($credentialsEmail)) {
                        $loginID = true;
                    }
                }
            }

            if ($loginID == false) {
                $response['status'] = 401;
                $response['message'] = 'Invalid Email or Phone Number.';
                // Call sendFailedLoginResponse() to check what is the issue in     authenticating user
                // and respond with the proper error message
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
            $id = $user->id;
            $phone_number = $user->phone_number;
            $random_otp = rand(100000, 999999);
            $ins_arr = ['otp' => $random_otp];
            if (empty($user)) {
                $response['status'] = 401;
                $response['message'] = 'Email does not exists.';
                return $this->sendError($response);
            } else {
                $qry = UserOtp::updateOrCreate(
                    ['master_user_id' => $id],
                    $ins_arr
                );
                $random = Str::random(30);
                try {
                    $AppURL = env('APP_URL');
                    $TemplateData = array(
                        'EMAIL' => $user->email,
                        'USER_NAME' => $user->name,
                        'OTP' => $random_otp
                    );
                    MailHelper::sendMail('LOGIN_OTP', $TemplateData);
                    $response['status'] = 200;
                    $response['message'] = 'Please check your email for the otp.';
                    return $this->sendResponse($response);
                } catch (Exception $exp) {
                    $response['status'] = 503;
                    $response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
                    return $this->sendError($response);
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
