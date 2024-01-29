<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Master\{MasterSociety,SubscriptionPlan,MasterUser,MasterDatabase};


class SocietyController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = MasterSociety::where([['status', 0]]);
        $data_query->select([
            'id',
            'society_name',
            'society_unique_code','phone_number','address','email','country_id','state_id','city','zipcode',
            'gst_number','pan_number', 'created_at'
        ]);
        return $data_query;
    }
    public function index(Request $request)
    {
        $data_query = $this->list_show_query();
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('society_unique_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('society_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "society_unique_code", "society_name", "phone_number"];
        return $this->commonpagination($request, $data_query, $fields);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

      
        if ($request->id > 0) {
            $existingRecord = MasterSociety::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if ($request->user_id > 0) {
            $Record = MasterUser::find($request->user_id);
            if (!$Record) {
                $response['status'] = 400;
                $response['message'] = 'No such user found for the provided user ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'society_name'                       =>'required|unique:master_socities,society_name,' . $id . ',id,deleted_at,NULL|max:255',
            'email' => 'required|email',
            'user_id'=>'required|integer',
            'address'                       =>'required',
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Society created successfully." : "Society updated successfully.";
            $ins_arr = [
                'society_name'                        => $request->society_name,
                'email'                               =>$request->email,
                'phone_number'                        =>$request->phone_number,
                'address'                             => $request->address,
                'adress2'                             => $request->adress2,
                'country_id'                          => $request->country_id,
                'state_id'                            =>$request->state_id,
                'city'                                => $request->city,
                'zipcode'                             => $request->zipcode,
                'gst_number'                          =>$request->gst_number,
                'pan_number'                          => $request->pan_number,
                'updated_by'                          => auth()->id(),
            ];
           

            if (empty($request->id)) {
                $obj = new MasterSociety();
                $ins_arr['society_unique_code'] = $obj->generateSocietyCode();
            }
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = MasterSociety::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $master_user=MasterUser::find($request->user_id);
            if ($master_user) {
             
                $decodedIds = is_array(json_decode($master_user->master_society_ids))
                ? json_decode($master_user->master_society_ids)
                : [];
                $mergedArray = array_unique(array_merge($decodedIds, [$qry->id]));
                $updatedIds = json_encode($mergedArray);
                $master_user->update([
                    'master_society_ids' => $updatedIds
                ]);
            } 
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = ['id' => $qry->id, 'society_name' => $qry->society_name,'phone_number'=>$qry->phone_number,
                 'address' => $qry->address];
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
            $response['message'] = 'Unable to save society.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data_query = $this->list_show_query();
        $data_query->where([['id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular society found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find society.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
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
    public function destroy(Request $request)
    {
        $terms = MasterSociety::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = MasterSociety::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $terms->destroy($request->id);
            $message = "Record Deleted Successfully !";
        } else {
            $message = "Record Not Found !";
        }
        $response['message'] = $message;
        $response['status'] = 200;
        return $this->sendResponse($response);
    }
}
