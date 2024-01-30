<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\{Tower, Wing};
use Illuminate\Support\Facades\DB;
// use App\Models\Master\Tower;
use Illuminate\Support\Facades\Crypt;
use App\Models\Master\MasterSociety;

class WingsController extends ResponseController
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
    function list_show_query()
    {
        $data_query = Wing::with(['tower' => function ($query) {
            $query->select(
                'id',
            'tower_name', 'societies_id', 'created_at'
            )->where([['status', 0]]);
        }]);
        $data_query->select([
            'id',
            'wings_name',
            'tower_id',
            'created_at'
        ]);
        return $data_query;
        
    }
    public function edit(Request $request)
    {
        if ($request->id > 0) {
            $existingRecord = Wing::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id =$request->id;
        $validator = Validator::make($request->all(), [
            'id' =>'required'
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $ins_arr = [
                'wings_name'                        =>$request->wings_name
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = Wing::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = "Wings updated successfully.";
                $data_query = $this->list_show_query();
                $data_query->where([['id', $qry->id]]);
                $result = $data_query->get()->toArray();
                $response['data'] = $result;
                return $this->sendResponse($response);
            } else {
                $response['status'] = 400;
                $response['message'] = "Wings updated successfully.";
                return $this->sendError($response);
            }
        } else {
            if ($qry) {
                $response['message'] = "Wings updated successfully.";
                $response['status'] = 200;
                return $this->sendResponse($response);
            }
            $response['message'] = 'Unable to save wing.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
    }
    public function delete(Request $request)
    {
        $terms = Wing::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Wing::updateOrCreate(
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
