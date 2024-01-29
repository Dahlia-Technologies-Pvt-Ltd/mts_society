<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\{Flat,Floor};

class FlatController extends Controller
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
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        $sql = "SELECT id FROM users WHERE master_society_id =  $societies_id";
        $results = DB::select($sql);
        $net_id=$results[0]->id;
        if ($request->id > 0) {
            $existingRecord = Flat::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        } //
        if ($request->floor_id > 0) {
            $existingRecord = Floor::find($request->floor_id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided floor ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'flat_name'                                    => 'required|unique:towers,tower_name,' . $id . ',id,deleted_at,NULL|max:255',
            
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Flat created successfully." : "Flat updated successfully.";
            ,[flat_name]
            ,[user_id]
            ,[floor_id]
            ,[status]
            ,[created_by]
            ,[updated_by]
            ,[deleted_at]
            ,[created_at]
            ,[updated_at]

            $ins_arr = [
                'societies_id'                        => $net_id,
                'flat_name'                     => $request->tower_name,
                'user_id'                     => $request->user_id,
                'floor_id'                     => $request->floor_id,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = Flat::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = ['id' => $qry->id,'child_society_id'=>$net_id,'master_societies_id' =>$societies_id , 
                'tower_name' => $qry->tower_name];
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
            $response['message'] = 'Unable to save tower.';
            $response['status'] = 400;
            return $this->sendError($response);
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
