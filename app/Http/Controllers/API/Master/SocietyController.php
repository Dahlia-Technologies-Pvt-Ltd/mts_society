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
        // return !empty($arr) ? json_encode(array_map('intval',array_values($arr))) : NULL;
        $data_query = MasterSociety::where([['status', 0]]);
        $data_query->select([
            'id',
            'society_name',
            'society_unique_code','phone_number','address','email','country_id','state_id','city_id','zipcode',
            'gst_number','pan_number', 'created_at'
        ]);
        return $data_query;
    }
    public function indexing(Request $request)
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
        // if ($request->subscription_plan_id > 0) {
        //     $Record = SubscriptionPlan::find($request->subscription_plan_id);
        //     if (!$Record) {
        //         $response['status'] = 400;
        //         $response['message'] = 'No such subscription plan found for the provided  subscription ID.';
        //         return $this->sendError($response);
        //     }
        // }

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
            // 'attachments'                          => 'required_without:template_content|file|mimes:pdf|max:5120',
            // 'documents'                          => $documents,
            'society_name'                       =>'required|unique:master_socities,society_name,' . $id . ',id,deleted_at,NULL|max:255',
            // 'owner_name'                       =>'required',
            'email' => 'required|email',
            'user_id'=>'integer',
            'address'                       =>'required',
            // 'documents'           =>$documents,


            // 'country_id'                       =>'required',
            // 'state_id'                       =>'required',

            // 'subscription_plan_id'                       =>'required|integer',


            // 'society_name'                       =>'required',
            // 'society_name'                       =>'required',
            // 'society_name'                       =>'required',
            // 'society_name'                       =>'required',
            // 'template_content'                     => 'required_without:attachments',
            // 'template_name'                        => 'required|unique:terms_conditions,template_name,' . $id . ',id,deleted_at,NULL|max:255',
            // 'is_mandatory'                         => 'required|integer|min:0|max:1',
            // 'default_spare_or_customer'            => 'required|integer|min:0|max:2',
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            // $filepath = NULL;
            // if ($request->hasFile('documents')) {
            //     $file = $request->file('documents');
            //     $fileName = time() . '_' . $file->getClientOriginalName();
            //     $filepath = $file->storeAs('uploads/master_society', $fileName);
            // } else if(isset($request['old_documents']) && !empty($request['old_documents'])){
            //     $filepath = str_replace(asset('storage') . '/', '',$request['old_documents']);
            // }

            $message = empty($request->id) ? "Society created successfully." : "Society updated successfully.";

            $ins_arr = [
                'society_name'                        => $request->society_name,
                // 'owner_name'                          => $request->owner_name,
                'email'                               =>$request->email,
                'phone_number'                        =>$request->phone_number,
                'address'                             => $request->address,
                'adress2'                             => $request->adress2,
                'country_id'                          => $request->country_id,
                'state_id'                            =>$request->state_id,
                'city_id'                             => $request->city_id,
                'zipcode'                             => $request->zipcode,
                'gst_number'                          =>$request->gst_number,
                'pan_number'                          => $request->pan_number,
                // 'subscription_plan_id'                => $request->subscription_plan_id,
                // 'payment_mode'                        => ($request->payment_mode === '1') ? 1 : 0,
                // 'payment_status'                      => ($request->payment_status === '1') ? 1 : 0,
                // 'documents'                           => 'abc',
                // 'currency_code'                       => $request->currency_code,
                // 'is_approved'                         => ($request->is_approved === '1') ? 1 : 0,
                // 'status'                              => $request->email,
                // 'is_renewal_plan'                     => ($request->is_renewal_plan === '0') ? 0 : 1,
                
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
    public function delete(Request $request)
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
