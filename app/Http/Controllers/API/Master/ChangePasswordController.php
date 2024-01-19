<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
// use App\Models\Master\{Country,State};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends ResponseController
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
    // public function changepassword(Request $request)
    // {
    //   print_r('tttt');die();  //
    // }

    public function changepassword(Request $request) : JsonResponse
    {
		if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
		$validator = \Validator::make($request->all(), [
			'curr_password' => 'required',
			'new_password' => [
				'required',
				'string',
				'min:8', // Minimum password length
				'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=]).*$/', // Include your strength criteria here
			],
			'confirm_password' => 'required|same:new_password', // Confirm password must match new password
		],
		[
			'curr_password.required' => 'Current Password is required.',
			'new_password.required' => 'New Password is required.',
			'new_password.min' => 'Password must be at least 8 characters long.',
			'new_password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number and one special character.',
			'confirm_password.required' => 'Confirm Password is required.',
			'confirm_password.same' => 'New Password and Confirm Password must match.',
		]);
	
		if ($validator->fails()) {
			return $this->validatorError($validator);
		} 
		else 
		{
			if (!(\Hash::check($request->curr_password, Auth::user()->password))) {
				// Check Current password with database
				$response['status'] = 403;
				$response['message'] = 'Your current password is incorrect.';
				return $this->sendError($response);
			}
			else if(strcmp($request->curr_password, $request->new_password) == 0){
				// Current password and new password same
				$response['status'] = 403;
				$response['message'] = 'New Password cannot be same as your current password.';
				return $this->sendError($response);
			}
			else if(strcmp($request->new_password, $request->confirm_password) != 0){
				// Current password and new password same
				$response['status'] = 403;
				$response['message'] = 'New Password and Confirm Password must be same.';
				return $this->sendError($response);
			}
			else {
				//Change Password
				$user = Auth::user();
				$user->password = Hash::make($request->new_password);
				$user->save();
				$response['status'] = 200;
				$response['message'] = 'Password changed successfully.';
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
