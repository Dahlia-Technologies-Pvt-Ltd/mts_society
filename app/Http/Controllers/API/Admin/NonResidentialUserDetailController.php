<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
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
        //  print_r( $data_query->get()->toArray());die();
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
        // print_r( $societies_id);die();
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
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
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
                    // 'street_address'=> $request->street_address,
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
                    // 'master_service_provider_ids'=> $request->master_service_provider_ids,
                    'management_company_name'=> $request->management_company_name,
                    // 'management_company_country_code'=> $request->management_company_country_code,
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
}
