<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\{MasterUser,UserOtp};
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Mail\CommonMail;
use Illuminate\Http\JsonResponse;
use App\Mail\SendMail;
use DB; 
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail; 
use App\Helpers\MailHelper;


class ForgotPasswordController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function forgotpassword(Request $request) : JsonResponse
	{
		$validator = \Validator::make($request->all(), 
			[
				'email' => 'required'
			],
			[
				'email.required' => 'Email field is required.'
			]
		);
		if ($validator->fails()) 
		{
			return $this->validatorError($validator);
		}
		else
		{
			// Load user from database 
			$user = MasterUser::getEmailSingle($request->email);
			if (empty($user)) {
				$response['status'] = 401;
				$response['message'] = 'Email does not exists.';
				return $this->sendError($response);
			}
			else{
				$user->forgot_password_token = Str::random(30);
				$user->forgot_password_token_time = Carbon::now()->addMinutes(30);
				$user->save();
				try{
					$AppURL = env('APP_URL');
					$TemplateData = array(						
						'EMAIL' => $user->email,
						'USER_NAME' => $user->name,
						'RESET_LINK' => "<a href='{$AppURL}/reset-password/{$user->forgot_password_token}'><button type='button' class='btn btn-primary'>Reset Password</button></a>",
                        // 'OTP' =>$random_otp 
					);
					MailHelper::sendMail('FORGOT_PASSWORD',$TemplateData);
					$response['status'] = 200;
					$response['message'] = 'Please check your email to reset your password.';
					return $this->sendResponse($response);
				} catch (Exception $exp) {
					$response['status'] = 503;
					$response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
					return $this->sendError($response);
				}
			}
		}
	}
	public function ResetPassword(Request $request) : JsonResponse
	{
		$validator = \Validator::make($request->all(), 
			[
				'token' => 'required',
				'new_password' => 'required|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%&*_-])[a-zA-Z0-9!@#$%&*_-].{7,}+$/',
				'con_password' => 'required|same:new_password',
			],
			[
				'token.required' => 'Please provide token',
				'new_password.required' => 'Please provide Password',
				'new_password.regex' => 'Password must contain  at least one lower case letter, one upper case letter, one digit, one special character and minimum 8 characters',
				'con_password.required' => 'Please provide Confirm Password',
				'con_password.same' => 'Password and Confirm Password does not match',
			]
		);
		if ($validator->fails()) 
		{
			return $this->validatorError($validator);
		}
		else
		{
			// Check user token exists / valid from database
			$user = MasterUser::getTokenSingle($request->token);
			if (empty($user)) {
				$response['status'] = 401;
				$response['message'] = 'Token expired or does not exists.';
				return $this->sendError($response);
			}
			else {
				//Change Password
				$user->password = Hash::make($request->new_password);
				$user->save();
				$response['status'] = 200;
				$response['message'] = 'Password reset successfully.';
				return $this->sendResponse($response);
			}
		}
	}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
