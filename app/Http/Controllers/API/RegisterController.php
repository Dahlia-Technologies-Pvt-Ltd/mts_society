<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Models\Master\{MasterUser, MasterSociety};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends ResponseController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:master_users',
            'phone_number' => 'required|string|unique:master_users',
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        }

        // Insert Master User
        $master_user = MasterUser::create([
            'user_code' => (new MasterUser())->generateUserCode(),
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->email),
        ]); 
        // Insert Master Society
        if($master_user){
            $master_society = MasterSociety::create([
                'society_unique_code' => (new MasterSociety())->generateSocietyCode(),
                'society_name' => $request->society_name,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'zipcode' => $request->zipcode,
                'created_by' => $master_user->id, // insert Master User Id
            ]);
        }      
        

        $response['status'] = 200;
        $response['message'] = 'User registered successfully.';

        // You can customize the response based on your requirements
        return $this->sendResponse($response);
    }
}