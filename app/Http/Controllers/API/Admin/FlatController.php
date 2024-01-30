<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\{Flat,Floor};

class FlatController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = Flat::join('floors', 'floors.id', '=', 'flats.floor_id')
        ->join('towers', 'towers.id', '=', 'floors.tower_id')->Leftjoin('wings', 'wings.id', '=', 'floors.wing_id')
        ->join('users', 'users.id', '=', 'flats.user_id');
        $data_query->select([
            'flats.id AS id',
            'towers.tower_name AS tower_name',
            'wings.wings_name',
            'flats.flat_name AS flat_name',
            'floors.id AS floor_id',
            'floors.floor_name AS floor_name',
            'user_id',
            'users.full_name AS full_name',
            'users.username AS username',
            'users.email AS email',
            'users.zipcode AS zipcode'
            // 'towers.tower_name AS tower_name',
            // 
            
        ]);
        return $data_query;
    }
    public function indexing(Request $request)
    {
        $data_query = $this->list_show_query();
        print_r($data_query->get()->toArray());die(); //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $societies_id = getsocietyid($request->header('society_id'));
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
            'flat_name'                                    => 'required|unique:flats,flat_name,' . $id . ',id,deleted_at,NULL|max:255',
            'floor_id'      =>'required|integer|min:1'
            
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Flat created successfully." : "Flat updated successfully.";
            $ins_arr = [
                'flat_name'                     => $request->flat_name,
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
                $response['data'] = ['id' => $qry->id,'user_id'=>$net_id,'floor_id' =>$qry->floor_id, 
                'flat_name' => $qry->flat_name];
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
            $response['message'] = 'Unable to save flat.';
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
    public function delete(string $id)
    {
        //
    }
}
