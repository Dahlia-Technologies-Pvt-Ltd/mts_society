<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\{ResidentialUserDetail,Parking,User,Flat,Society};
use App\Models\Master\{MasterSociety,MasterUser};

class ResidentialUserDetailController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = ResidentialUserDetail::join('users', 'users.id', '=', 'residential_user_details.user_id')->join('flats', 'flats.id', '=', 'residential_user_details.flat_id')
        ->join('floors', 'floors.id', '=', 'flats.floor_id')
        ->Leftjoin('wings', 'wings.id', '=', 'floors.wing_id')
        ->join('towers', 'towers.id', '=', 'floors.tower_id');
        $data_query->select([
            'users.id AS edit_id',
            'residential_user_details.id AS id',
            'users.name',
            'users.phone_number',
            'users.user_code AS user_code',
            'users.email',
            'parking_ids',
            'vehicle_types',
            'flats.id AS flat_id',
            'flats.flat_name AS flat_name',
            'towers.id AS towers_id',
            'towers.tower_name AS tower_name',
            'wings.id AS wings_id',
            'wings.wings_name',
            'floors.id AS floor_id',
            'floors.floor_name AS floor_name'
        ]);
        return $data_query;
    }
    public function index(Request $request)
    {
        $data_query = $this->list_show_query();
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('floors.floor_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('towers.tower_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.phone_number', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('flats.flat_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('towers.tower_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('wings.wings_name', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "floors.floor_name", "towers.tower_name", "wings.wings_name","users.phone_number","users.name","users.email","flats.flat_name"];
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
    if(isset($request->parking_ids)){
        $parking_ids = json_decode($request->parking_ids, true);
        foreach ($parking_ids as $key => $parkingidValue) {
            $existingparking = Parking::
                where('id', $parkingidValue)
                ->exists();
        
            if (!$existingparking) {
                $response['status'] = 400;
                $response['message'] =' Parking area doesnt exists.';
                return $this->sendError($response);
            }
        }
    }
    if ($request->flat_id > 0) {
        $existingRecord = Flat::find($request->flat_id);
        if (!$existingRecord) {
            $response['status'] = 400;
            $response['message'] = 'Record not found for the provided ID.';
            return $this->sendError($response);
        }
    }
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
        'name'  => 'required',
        'phone_number'                  => 'required|digits:10|unique:users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
        'email'                        => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL|max:255',
        'phone_number'                  => 'required|digits:10|unique:users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
        'username'                        => 'required|unique:users,username,' . $id . ',id,deleted_at,NULL|max:255',
        
    ]);

    if ($validator->fails()) {
        return $this->validatorError($validator);
    } else {
        $message = empty($request->id) ? "Residential user created successfully." : "Residential user updated successfully.";

        $ins_arr = [
                'name'                        => $request->name,
                'username'                    => $request->username,
                'email'                       => $request->email,
                'phone_number'                => $request->phone_number,
                'master_society_ids'          =>jsonEncodeIntArr([$societies_id]),
                'country_id'                  => $master_society_country_id,
                'state_id'                    => $master_society_state_id,
                'zipcode'                     => MasterSociety::find($master_societies_id)->zipcode,
                'usertype'                    => 0,//resident user 
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
            ['id' => $existingRecord->master_user_id],
            $ins_arr
        );
        if($qry){ 
            $ins_arr2 = [
                'name'                        => $request->name,
                    'username'                    => $request->username,
                    'email'                       => $request->email,
                    'phone_number'                => $request->phone_number,
                    'master_society_ids'          =>jsonEncodeIntArr([$societies_id]),
                    'user_code'                   =>$qry->user_code,
                    'country_id'                  => $master_society_country_id,
                    'state_id'                    => $master_society_state_id,
                    'zipcode'                     => MasterSociety::find($master_societies_id)->zipcode,
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
            $ins_arr3 = [
                'societies_id'                        => $societies_id,
                    'flat_id'                       => $request->flat_id,
                    'vehicle_types'                =>  isset($request->vehicle_types)?jsonEncodeIntArr(json_decode($request->vehicle_types)):jsonEncodeIntArr([]),
                    'parking_ids'          =>isset($request->parking_ids)?jsonEncodeIntArr(json_decode($request->parking_ids)):jsonEncodeIntArr([]),
                ];
               
                $qry3 = ResidentialUserDetail::updateOrCreate(
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
    if (request()->is('api/*')) {
        if ($qry3) {
            $data_query = $this->list_show_query();
            $data_query->where('residential_user_details.id',  $qry3->id);
            $queryResult = $data_query->get();
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
        $subs = ResidentialUserDetail::find($request->id);
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
        $data_query = $this->list_show_query();
        $data_query->where([['residential_user_details.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular resident user found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find resident user.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
    }
}

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }

