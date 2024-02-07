<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\{MasterServiceProvider};

class MasterServiceProviderController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
    $data_query = MasterServiceProvider::where([['status', 0]]);
    $data_query->select([
        'id',
        'societies_id',
        'name',
        'is_daily_helper', 'created_at'
    ]);
    return $data_query;
}
public function index(Request $request)
{
    $data_query = $this->list_show_query();
    if (!empty($request->keyword)) {
        $keyword = $request->keyword;
        $data_query->where(function ($query) use ($keyword) {
            $query->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('is_daily_helper', 'LIKE', '%' . $keyword . '%');
        });
    }
    $fields = ["id", "name", "is_daily_helper"];
    return $this->commonpagination($request, $data_query, $fields);  //
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        if ($request->id > 0) {
            $existingRecord = MasterServiceProvider::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'name' =>  'required|unique:master_service_providers,name,' . $id . ',id,deleted_at,NULL|max:255'
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Service provider created successfully." : "Service provider updated successfully.";
            $ins_arr = [
                'societies_id'                        => $societies_id,
                'name'                     => $request->name,
                'is_daily_helper'                          =>isset($request->is_daily_helper)?$request->is_daily_helper:0,//0-no help,1-yes help
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = MasterServiceProvider::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            $data_query = $this->list_show_query();
            $data_query->where('id', $qry->id);
            $queryResult = $data_query->get();
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = $queryResult;
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
            $response['message'] = 'Unable to save sevice provider.';
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
            $message = "Particular subscription found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find subscription.';
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
        $subs = MasterServiceProvider::find($request->id);
        if ($subs) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = MasterServiceProvider::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $subs->destroy($request->id);
            $message = "Record Deleted Successfully !";
        } else {
            $message = "Record Not Found !";
        }
        $response['message'] = $message;
        $response['status'] = 200;
        return $this->sendResponse($response);
    }
}
