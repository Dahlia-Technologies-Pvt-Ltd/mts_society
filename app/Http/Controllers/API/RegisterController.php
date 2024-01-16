<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Models\Master\{MasterUser, MasterSociety, UserSubscription, SubscriptionPlan, MasterDatabase};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Services\DatabaseService;

class RegisterController extends ResponseController
{
    //Register method
    public function register(Request $request): JsonResponse
    {
        echo "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa";die;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:master_users',
            'phone_number' => 'required|string|unique:master_users',
            'society_name' => 'required|string|max:255|unique:master_societies',
            'address' => 'required|string|max:255',
            'country_id' => 'required|integer',
            'city_id' => 'required|integer',
            'zipcode' => 'required|integer',
        ]);
        //Through validation Error
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
            //Update Master User Column master_society_ids newly generated master society ids as an array
            if ($master_society) {
                $master_user->update([
                    'master_society_ids' => array_merge($master_user->master_society_ids, [$master_society->id]),
                ]);
            }
            //Fetch data master Subscription through master_subscription_id and insert respective data in to user subscription table
            $subscriptionData = SubscriptionPlan::select('id, subscription_plan, price, frequency, features')
            ->where('id', $request->master_subscription_id)->first();
            //Insert Into User Subscription
            $user_subscription = UserSubscription::create([
                'master_subscription_id' => $request->master_subscription_id,
                'master_user_id' => $master_user->id,
                'master_socities_id' => $master_society->id,
                'subscription_plan' => $subscriptionData->subscription_plan,
                'price' => $subscriptionData->price,
                'frequency' => $subscriptionData->frequency,
                'features' => $subscriptionData->features,
            ]);
            //Call DatabaseService to Create dynamic database
            $dbName = $this->cleanName($request->society_name);
            //Here we use Crypt facade for store the database credential as encrypted format
            $encryptedDbName = Crypt::encryptString($dbName);
            $encryptedDbUid = Crypt::encryptString($dbName);
            $encryptedDbPwd = Crypt::encryptString($dbName.'@123');
            //Insert Into Master Database
            $master_database = MasterDatabase::create([
                'databasename' => $encryptedDbName,
                'databaseuid' => $encryptedDbUid,
                'databasepwd' => $encryptedDbPwd,
                'master_user_id' => $master_user->id,
                'master_socities_id' => $master_society->id,
            ]);
            //If Master database inserted successfully then call DataServices
            ($master_database) ? $databaseValue = (new DatabaseService())->createDatabase($params = ['dbname' => $dbName]) : '';
        }
        
        $response['status'] = 200;
        $response['message'] = 'User registered successfully.';

        // You can customize the response based on your requirements
        return $this->sendResponse($response);
    }

    // Remove special characters and replace spaces with underscores
    function cleanName($societyName)
    {
        $cleanedName = preg_replace('/[^\w\s]/', '', $societyName);
        $cleanedName = str_replace(' ', '_', $cleanedName);
        // Convert to lowercase
        $cleanedName = strtolower($cleanedName);
        return $cleanedName;
    }
    
}