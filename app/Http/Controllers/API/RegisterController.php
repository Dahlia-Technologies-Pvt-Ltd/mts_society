<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Models\Master\{MasterUser, MasterSociety, UserSubscription, MasterSubscription, MasterDatabase};
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:master_users',
            'phone_number' => 'required|string|unique:master_users',
            'society_name' => 'required|string|max:255|unique:master_socities',
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
            'username' => $this->cleanName($request->name),
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->email),
            'usertype' => 1,
            'country_id' =>isset($request->country_id)?$request->country_id:'-',
            'state_id' =>isset($request->state_id)?$request->state_id:'-',
            'city_id' =>isset($request->city_id)?$request->city_id:'-',
        ]); 
        // Insert Master Society
        if($master_user){
            $master_society = MasterSociety::create([
                'society_unique_code' => (new MasterSociety())->generateSocietyCode(),
                'society_name' => $request->society_name,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'state_id' => isset($request->state_id)?$request->state_id:'-',
                'zipcode' => $request->zipcode,
                'created_by' => $master_user->id, // insert Master User Id
            ]);
            //Update Master User Column master_society_ids newly generated master society ids as an array
            if ($master_society) {
                $master_society_ids = json_decode($master_user->master_society_ids, true) ?? [];
                $master_society_ids[] = $master_society->id;
                $master_user->update([
                    'master_society_ids' => json_encode($master_society_ids),
                ]);
            }            

            //Fetch data master Subscription through master_subscription_id and insert respective data in to user subscription table
            $masterSubscriptionData = MasterSubscription::select('id', 'subscription_plan', 'price', 'frequency', 'features')
            ->where('id', $request->master_subscription_id)->first();
            //Insert Into User Subscription
            $user_subscription = UserSubscription::create([
                'master_subscription_id' => $request->master_subscription_id,
                'master_user_id' => $master_user->id,
                'master_socities_id' => $master_society->id,
                'subscription_plan' => $masterSubscriptionData->subscription_plan,
                'price' => $masterSubscriptionData->price,
                'frequency' => $masterSubscriptionData->frequency,
                'features' => $masterSubscriptionData->features,
            ]);
            //Call DatabaseService to Create dynamic database
            $dbName = $this->cleanName($request->society_name);
            $dbPassword = $dbName.'@123';//Generate random number for database
            //Here we use Crypt facade for store the database credential as encrypted format
            // $encryptedDbName = Crypt::encryptString($dbName);
            // $encryptedDbUid = Crypt::encryptString($dbName);
            // $encryptedDbPwd = Crypt::encryptString($dbPassword);
            $encryptedDbName = $dbName;
            $encryptedDbUid = $dbName;
            $encryptedDbPwd = $dbPassword;
            //Insert Into Master Database
            $master_database = MasterDatabase::create([
                'databasename' => $encryptedDbName,
                'databaseuid' => $encryptedDbUid,
                'databasepwd' => $encryptedDbPwd,
                'master_user_id' => $master_user->id,
                'master_socities_id' => $master_society->id,
            ]);
            //If Master database inserted successfully then call DataServices
            ($master_database) ? $databaseValue = (new DatabaseService())->createDatabase($params = ['dbname' => $dbName, 'dbpassword' => $dbPassword]) : '';
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

    //Generate Random Password
    function generatePassword($len){
        $az = range("a","z");
        $AZ = range("A","Z");
        $num = range(0,9);
        $password = array_merge($az,$AZ,$num);
        return substr(str_shuffle(implode("",$password)),0, $len);
    }
    
}