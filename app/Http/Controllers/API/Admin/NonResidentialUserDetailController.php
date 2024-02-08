<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Helpers\MailHelper;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Admin\{NonResidentialUserDetail,Parking,User,Flat,Society,MasterServiceProvider,Tower};
use App\Models\Master\{MasterSociety,MasterUser};

class NonResidentialUserDetailController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    
     function list_show_query($a)
     {
        if($a==0){ $data_query = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id')->where('team_type','=',0);
             $data_query->select([
                'users.id AS edit_id',
                         'non_residential_user_details.id AS id',
                         'non_residential_user_details.assigned_tower_ids AS assigned_tower_ids',
                         'non_residential_user_details.master_service_provider_ids AS master_service_provider_ids',
                         'non_residential_user_details.team_type AS team_type',
                         'users.name',
                         'users.country_id',
                         'users.state_id',
                         'users.phone_number',
                         'users.user_code AS user_code',
                         'users.email',
                         'users.user_code',
             ]);
             return $data_query;
            }

            if($a==1){ 
                $data_query = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id')->where('team_type','=',1);
                 $data_query->select([
                    'users.id AS edit_id',
                         'non_residential_user_details.id AS id',
                         'non_residential_user_details.assigned_tower_ids AS assigned_tower_ids',
                         'non_residential_user_details.master_service_provider_ids AS master_service_provider_ids',
                         'non_residential_user_details.team_type AS team_type',
                         'users.name',
                         'users.country_id',
                         'users.state_id',
                         'users.phone_number',
                         'users.user_code AS user_code',
                         'users.email',
                         'users.user_code',
                 ]);
                 return $data_query;
                }else{
                    $data_query = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id')->where('team_type','=',2);
                     $data_query->select([
                         'users.id AS edit_id',
                         'non_residential_user_details.id AS id',
                         'non_residential_user_details.assigned_tower_ids AS assigned_tower_ids',
                         'non_residential_user_details.master_service_provider_ids AS master_service_provider_ids',
                         'non_residential_user_details.team_type AS team_type',
                         'users.name',
                         'users.country_id',
                         'users.state_id',
                         'users.phone_number',
                         'users.user_code AS user_code',
                         'users.email',
                     ]);
                     return $data_query;
                    }
        
     }
     public function index(Request $request)
     {
        $a=$request->team_type;
         $data_query = $this->list_show_query($a);
         if (!empty($request->keyword)) {
             $keyword = $request->keyword;
             $data_query->where(function ($query) use ($keyword) {
                 $query->where('users.user_code', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('users.email', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('users.name', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('users.phone_number', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('users.email', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('non_residential_user_details.team_type', 'LIKE', '%' . $keyword . '%');
             });
         }
         $fields = ["id", "users.user_code", "users.phone_number","users.name","users.email"];
         return $this->commonpagination($request, $data_query, $fields);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $societies_id = getsocietyid($request->header('society_id'));
        $master_societies_id=Society::find($societies_id)->master_society_id;
        $master_society_country_id=MasterSociety::find($master_societies_id)->country_id;
        $master_society_state_id=MasterSociety::find($master_societies_id)->state_id;
        if($request->team_type == 1)
    if(isset($request->master_service_provider_ids)){
        $providers_ids = json_decode($request->master_service_provider_ids, true);
        foreach ($providers_ids as $key => $providersidValue) {
            $existingprovider = MasterServiceProvider::
                where('id', $providersidValue)
                ->exists();
        
            if (!$existingprovider) {
                $response['status'] = 400;
                $response['message'] =' Provider doesnt exists.';
                return $this->sendError($response);
            }
        }
    }
    if($request->team_type == 0)
    if(isset($request->assigned_tower_ids)){
        $tower_ids = json_decode($request->assigned_tower_ids, true);
        foreach ($tower_ids as $key => $toweridValue) {
            $existingtower = Tower::
                where('id', $toweridValue)
                ->exists();
        
            if (!$existingtower) {
                $response['status'] = 400;
                $response['message'] =' Tower doesnt exists.';
                return $this->sendError($response);
            }
        }
    }
    $id_req = null;
    if ($request->id > 0) {
        $existingRecord = User::find($request->id);
        if (!$existingRecord) {
            $response['status'] = 400;
            $response['message'] = 'Record not found for the provided ID.';
            return $this->sendError($response);
        }
        $id_req=$existingRecord->master_user_id;
    }
    $id = empty($request->id) ? 'NULL' : $request->id;
    
    $validator = Validator::make($request->all(), [
        'full_name'  => 'required',
        'phone_number'                  => 'required|digits:10|unique:users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
        'email'                        => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL|max:255',
        'phone_number'                  => 'required|digits:10|unique:users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
        'username'                        => 'required|unique:users,username,' . $id . ',id,deleted_at,NULL|max:255',
        'country_id' => 'required|integer',
        'state_id' => 'required|integer',
        'country_code'=>'required',
        'pan_no'=>'required',
        'aadhaar_no'=>'required',
        'team_type'=>'required|integer'
        
    ]);
    if($request->team_type == 0){
        $validator = Validator::make($request->all(), [
            'assigned_tower_ids'  => 'required',  
            'management_company_name' => 'required',
            'management_company_phone_number' => 'required',
        ]);
    }
    if($request->team_type == 1){
        $validator = Validator::make($request->all(), [
            'master_service_provider_ids'  => 'required',  
           
        ]);
    }
    if ($validator->fails()) {
        return $this->validatorError($validator);
    } else {
        $message = empty($request->id) ? "Non Residential user created successfully." : "Non Residential user updated successfully.";
        $ins_arr = [
                'name'                        => $request->full_name,
                'username'                    => $request->username,
                'email'                       => $request->email,
                'phone_number'                => $request->phone_number,
                'master_society_ids'          =>jsonEncodeIntArr([$societies_id]),
                'country_id'                  => $request->country_id,
                'state_id'                    =>$request->state_id,
                'city'                    =>$request->city,
                'zipcode'                     => $request->zipcode,
                'usertype'                    => 0,//other user 
                'updated_by'                           => auth()->id(),
            ];
            if($request->team_type == 0){
                $ins_arr2['status'] = 1;//inactive 
            }
            if (empty($request->id)) {
                $obj = new MasterUser();
                $ins_arr['user_code'] = $obj->generateUserCode();
            }
        if (!$request->id) {
            $ins_arr['created_by'] = auth()->id();
        } else {
            $ins_arr['updated_by'] = auth()->id();
        }
        $qry = MasterUser::updateOrCreate(
            ['id' => $id_req],
            $ins_arr
        );
        if($qry){ 
            $ins_arr2 = [
                'name'                        => $request->full_name,
                    'username'                    => $request->username,
                    'email'                       => $request->email,
                    'phone_number'                => $request->phone_number,
                    'master_society_ids'          =>jsonEncodeIntArr([$societies_id]),
                    'user_code'                   =>$qry->user_code,
                    'country_id'                  => $request->country_id,
                    'state_id'                    =>$request->state_id,
                    'city'                    =>$request->city,
                    'zipcode'                     => $request->zipcode,
                    'usertype'                    => 0,//resident user 
                    'updated_by'                           => auth()->id(),
                ];
                if($request->team_type == 0){
                    $ins_arr2['status'] = 1;//inactive 
                }
            if (!$request->id) {
                $ins_arr2['created_by'] = auth()->id();
            } else {
                $ins_arr2['updated_by'] = auth()->id();
            }
            $qry2 = User::updateOrCreate(
                ['master_user_id' => $qry->id],
                $ins_arr2
            );
             if($qry2){
                if( $request->team_type == 2){ 
                    $ins_arr3 = [
                    'society_ids'=>jsonEncodeIntArr([$societies_id]),
                     'full_name' => $request->full_name, 
                     'email'=> $request->email,
                     'country_code'=> $request->country_code,
                    'phone_number'=> $request->phone_number,
                    'country'=> $request->country_id,
                    'state'=> $request->state_id,
                     'city'=> $request->city,
                     'zipcode'=> $request->zipcode,
                     'aadhaar_no'=> $request->aadhaar_no,
                     'pan_no'=> $request->pan_no,
                    'team_type'=> 2,//'0-Facility Manager,1-Service Provider,2-Security Guard'
          
                    ];
                }else if($request->team_type == 1){
                    $ins_arr3 = [
                    'society_ids'=>jsonEncodeIntArr([$societies_id]),
                     'full_name' => $request->full_name, 
                     'email'=> $request->email,
                     'country_code'=> $request->country_code,
                    'phone_number'=> $request->phone_number,
                    'street_address'=> $request->street_address,
                    'country'=> $request->country_id,
                    'state'=> $request->state_id,
                     'city'=> $request->city,
                     'zipcode'=> $request->zipcode,
                     'aadhaar_no'=> $request->aadhaar_no,
                     'pan_no'=> $request->pan_no,
                    'master_service_provider_ids'=> isset($request->master_service_provider_ids)?jsonEncodeIntArr(json_decode($request->master_service_provider_ids)):jsonEncodeIntArr([]),
                    'team_type'=> 1,//'0-Facility Manager,1-Service Provider,2-Security Guard'
          
                    ];
                }else{
                    $ins_arr3 = [
                    'society_ids'=>jsonEncodeIntArr([$societies_id]),
                     'full_name' => $request->full_name, 
                     'email'=> $request->email,
                     'country_code'=> $request->country_code,
                    'phone_number'=> $request->phone_number,
                    'street_address'=> $request->street_address,
                    'country'=> $request->country_id,
                    'state'=> $request->state_id,
                     'city'=> $request->city,
                     'zipcode'=> $request->zipcode,
                     'aadhaar_no'=> $request->aadhaar_no,
                     'pan_no'=> $request->pan_no,
                     'assigned_tower_ids'=> isset($request->assigned_tower_ids)?jsonEncodeIntArr(json_decode($request->assigned_tower_ids)):jsonEncodeIntArr([]),
                    'management_company_name'=> $request->management_company_name,
                    'management_company_phone_number'=> $request->management_company_phone_number,
                    'team_type'=> 0,//'0-Facility Manager,1-Service Provider,2-Security Guard'
                    ];   
                }
           
               
                $qry3 = NonResidentialUserDetail::updateOrCreate(
                    ['user_id' =>$qry2->id],
                    $ins_arr3
                );
        }
        else{
            $response['message'] = 'Unable to find residential user.';
            $response['status'] = 400;
            return $this->sendError($response);
        }}else{
            $response['message'] = 'Unable to find residential user.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
      
       
    }
    $data_query = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id');
     $data_query->select([
        'users.id AS edit_id',
        'non_residential_user_details.id AS id',
        'non_residential_user_details.assigned_tower_ids AS assigned_tower_ids',
        'non_residential_user_details.master_service_provider_ids AS master_service_provider_ids',
        'non_residential_user_details.team_type AS team_type',
        'users.name',
        'users.country_id',
        'users.state_id',
        'users.phone_number',
        'users.user_code AS user_code',
        'users.email',
        'users.user_code',
     ]);
    $data_query->where([['non_residential_user_details.id', $qry3->id]]);
    $queryResult = $data_query->get();
    if (request()->is('api/*')) {
        if ($qry3) {
            $response['status'] = 200;
            $response['message'] = $message;
            $response['data'] =  $queryResult ;
            return $this->sendResponse($response);
        } else {
            $response['status'] = 400;
            $response['message'] = $message;
            return $this->sendError($response);
        }
    } else {
        if ($qry) {
            $response['message'] = $message;
            $response['status'] = 200;
            return $this->sendResponse($response);
        }
        $response['message'] = 'Unable to save residential user.';
        $response['status'] = 400;
        return $this->sendError($response);
    }
    }
    public function sendinvitefacilitymanager(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id')); 
        if ($request->user_id > 0) {
            $existingRecordfm = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id')->where('team_type','=',0)->where('users.id','=',$request->user_id) ->where('users.status', '=', 1)->first();
            if (!$existingRecordfm) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }  
        $id = empty($request->id) ? 'NULL' : $request->id;
    
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required',
        ]);
        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            // $message = empty($request->id) ? "Non Residential user created successfully." : "Non Residential user updated successfully.";
           return  $this->sendInvite($request->user_id);
    }
}

public function acceptinvitefacilitymanager(Request $request): JsonResponse
	{
		$validator = \Validator::make(
			$request->all(),
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

		if ($validator->fails()) {
			return $this->validatorError($validator);
		} else {
                $masteruser = MasterUser::getTokenSingle($request->token);
                if (empty($masteruser)) {
                    $response['status'] = 401;
                    $response['message'] = 'Token expired or does not exists.';
                    return $this->sendError($response);
                }
                else {
                    //Change Password
                    $user = User::where('master_user_id',$masteruser->id)->first();
                    if(($user->status == 0)){
                        $response['status'] = 401;
                        $response['message'] = 'You account is already been activated.';
                        return response()->json($response, 401);
                    }
     
                    $masteruser->password = Hash::make($request->new_password);
                    $masteruser->status = 0;
                    $user->status = 0;
                    $user->save();
                    $masteruser->forgot_password_token = null;
                    $masteruser->forgot_password_token_time = null;
                    $masteruser->save();
				$userEmail = $user->email;
				$userName = $user->name;
					$send_mail_type = 'THANKYOU_FACILITY_MANAGER_SEND_INVITE';
				try {
					$AppURL = env('APP_URL');
					$TemplateData = array(
						'EMAIL' => $userEmail,
						'USER_NAME' => $userName,
					);
					MailHelper::sendMail($send_mail_type, $TemplateData);
					$response['status'] = 200;
					$response['message'] = 'You account has been activated!';
					return $this->sendResponse($response);
				} catch (Exception $exp) {
					$response['status'] = 503;
					$response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
					return $this->sendError($response);
				}
                }
				
              
			}
		}
	
    public function destroy(Request $request)
    {
        $subs = NonResidentialUserDetail::find($request->id);
        $users = User::find($subs->user_id);
        $masterusers = MasterUser::find($users->master_user_id);
        if ($subs) {
            $subs->destroy($request->id);
            $users->destroy($subs->user_id);
            $masterusers->destroy($users->master_user_id);
            $message = "Record Deleted Successfully !";
        } else {
            $message = "Record Not Found !";
        }
        $response['message'] = $message;
        $response['status'] = 200;
        return $this->sendResponse($response);
    }
    public function show(string $id)
    {
        $data_query = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id');
         $data_query->select([
            'users.id AS edit_id',
            'non_residential_user_details.id AS id',
            'non_residential_user_details.assigned_tower_ids AS assigned_tower_ids',
            'non_residential_user_details.master_service_provider_ids AS master_service_provider_ids',
            'non_residential_user_details.team_type AS team_type',
            'users.name',
            'users.country_id',
            'users.state_id',
            'users.phone_number',
            'users.user_code AS user_code',
            'users.email',
            'users.user_code',
         ]);
        $data_query->where([['non_residential_user_details.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular non resident user found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find non resident user.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
        
    }
   
    public function sendInvite($a)
	{
		$id = $a;
			$user_query = User::where('id', $id);
			if (!$user_query->exists()) {
				$response['message'] = 'Unable to find user.';
				$response['status'] = 400;
				return $this->sendError($response);
			} else {
				$user = $user_query->first();
                $masterusers = MasterUser::find($user->master_user_id);
                $masterusers->forgot_password_token = Str::random(30);
				$masterusers->forgot_password_token_time = Carbon::now()->addMinutes(30);
				$masterusers->save();
					$send_mail_type = 'FACILITY_MANAGER_SEND_INVITE';
				$userEmail = $user->email;
				$userName = $user->name;
				try {
					$AppURL = env('APP_URL');
					$TemplateData = array(
						'EMAIL' => $userEmail,
						'USER_NAME' => $userName,
						'INVITE_LINK' => "<a href='".$AppURL."/accept-invite?t=".$masterusers->forgot_password_token."'><button type='button' class='btn btn-primary'>Accept Invitation</button></a>",
					);
					MailHelper::sendMail($send_mail_type, $TemplateData);
					$response['status'] = 200;
					$response['message'] = 'Invitation sent successfully!';
					return $this->sendResponse($response);
				} catch (Exception $exp) {
					$response['status'] = 503;
					$response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
					return $this->sendError($response);
				}
		}
	}
}
