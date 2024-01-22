<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\MasterUser;
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
				$random = Str::random(30);
				// $user->forgot_password_token_time = Carbon::now()->addMinutes(30);
				// $user->save();
				try{
					$AppURL = env('APP_URL');
                    // print_r($AppURL);die();
					$TemplateData = array(						
						'EMAIL' => $user->email,
						'USER_NAME' => $user->name,
						'RESET_LINK' => "<a href='{$AppURL}/reset-password/{$random}'><button type='button' class='btn btn-primary'>Reset Password</button></a>"
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
