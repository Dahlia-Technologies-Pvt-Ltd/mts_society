<?php

namespace App\Http\Controllers\API;

use App\Models\MasterUser;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

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
                'password' => 'required'
            ],
            [
                'email.required' => 'Email is required.',
                'password.required' => 'Password is required.'
            ]
        );

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            // Array to verify credentials using user_code
            $credentialsUserCode = array(
                'username' => $request->email, 'password' => $request->password, 'status' => 0,
            );

            $loginID = false; // Default false to send the response accordingly in the end
            // Attempt to authorize using username
            if (Auth::attemptWhen($credentialsUserCode)) {
                $loginID = true;
            } else {
				if (is_numeric($request->email)) {
					// Array to verify credentials using phone number
					$credentialsEmail = array(
						'phone_number' => $request->email, 'password' => $request->password, 'status' => 0,
					);
				}else{
					 // Array to verify credentials using email
					$credentialsEmail = array(
						'email' => $request->email, 'password' => $request->password, 'status' => 0,
					);
				}
               
                // Attempt to authorize using email
                if (Auth::attemptWhen($credentialsEmail)) {
                    $loginID = true;
                }
            }

            if ($loginID == false) {
                $response['status'] = 401;
                $response['message'] = 'Invalid User Id or Email.';
                // Call sendFailedLoginResponse() to check what is the issue in authenticating user
                // and respond with the proper error message
                return $this->sendFailedLoginResponse($request);
            } else {
                $user = Auth::user();
                $user['token'] = $user->createToken('MTSSOCIETY')->plainTextToken;
                $response['status'] = 200;
                $response['message'] = 'User authenticated successfully.';
                $response['data'] = $user->only(['id', 'username', 'name', 'email', 'usertype', 'phone_number', 'token']);
                return $this->sendResponse($response);
            }
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            $token = Auth::user()->currentAccessToken(); // Get the current token
            $token->delete(); // Delete the current token
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