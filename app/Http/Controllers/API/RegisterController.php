<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use App\Models\Master\{MasterUser, UserOtp, MasterSociety, UserSubscription, MasterSubscription, MasterDatabase, State, Country};
use App\Models\CopyMasterSociety;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegisterController extends ResponseController
{

    //Register method
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:master_users',
            'phone_number' => 'required|string|unique:master_users',
            'password' => 'required',
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
            'password' => Hash::make($request->password),
            'usertype' => 1,
            'country_id' => isset($request->country_id) ? $request->country_id : '-',
            'state_id' => isset($request->state_id) ? $request->state_id : '-',
            'city' => isset($request->city) ? $request->city : '-',
        ]);
        // Insert Master Society
        if ($master_user) {
            $master_society = MasterSociety::create([
                'society_unique_code' => (new MasterSociety())->generateSocietyCode(),
                'society_name' => $request->society_name,
                'address' => $request->address,
                'country_id' => $request->country_id,
                'city' => $request->city,
                'state_id' => isset($request->state_id) ? $request->state_id : '-',
                'zipcode' => $request->zipcode,
                'created_by' => $master_user->id, // insert Master User Id
            ]);
            //Update Master User Column master_society_ids newly generated master society ids as an array
            if ($master_society) {
                $master_society_ids = jsonEncodeIntArr($master_user->master_society_ids, true) ?? [];
                $master_society_ids[] = $master_society->id;
                $master_user->update([
                    'master_society_ids' => jsonEncodeIntArr($master_society_ids),
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
            $dbName = 'soc_' . $this->cleanName($request->society_name);
            $dbPassword = $dbName . '@123'; //Generate random number for database
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
            Log::info('Reg 106 ' . DB::connection()->getDatabaseName());
            //If Master database inserted successfully then call DataServices
            ($master_database) ? $databaseValue = (new DatabaseService())->createDatabase(['dbname' => $dbName, 'dbpassword' => $dbPassword]) : '';
            $response['data'] = $databaseValue;
            Log::info('Reg 110 ' . DB::connection()->getDatabaseName());
            (new CopyMasterSociety())->masterToSociety(['databaseName' => $databaseValue['dbname'], 'databasePassword' => $databaseValue['dbpassword'], 'master_user_id' => $master_user->id, 'master_socities_id' => $master_society->id]);
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
    function generatePassword($len)
    {
        $az = range("a", "z");
        $AZ = range("A", "Z");
        $num = range(0, 9);
        $password = array_merge($az, $AZ, $num);
        return substr(str_shuffle(implode("", $password)), 0, $len);
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
            $otp_params['otp'] = $request->otp;
            $otp_params['keyword'] = $request->email;
            $user_otp_obj = new UserOtp();
            $opt_user_id = $user_otp_obj->verifyOtp($otp_params);
            if ($opt_user_id > 0) {
                $user_status = MasterUser::find($opt_user_id);
                $user_status->status = 2; //waiting for approval
                $user_status->save();

                $response['status'] = 200;
                $response['message'] = 'User registered successfully.';
                $response['data'] = $user_status->only(['id', 'username', 'name', 'user_code', 'email', 'usertype', 'status', 'phone_number', 'token', 'profile_picture']);
                return $this->sendResponse($response);
            } else {
                $response['status'] = 400;
                $response['message'] = 'Invalid otp or the time has expired';
                return $this->sendError($response);
            }
        }
    }
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
                'master_society_id' => 'required',
                'city' => 'required|string'
            ],
            [
                'email.required' => 'Email is required and should be unique.',
                'country_id.required' => 'Country is required.',
                'state_id.required' => 'State is required.'
            ]
        );
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
                'master_society_ids'  => jsonEncodeIntArr([$request->master_society_id]),
                'usertype' => 0, //0-user(resident)
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city' => $request->city,
                'user_code' => $obj->generateUserCode(),
                'status' => 1 //inactive
            ]);
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
}
