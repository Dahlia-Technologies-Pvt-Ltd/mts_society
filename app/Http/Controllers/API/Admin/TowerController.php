<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\Tower;
use Illuminate\Support\Facades\Crypt;
use App\Models\Master\MasterSociety;

class TowerController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */

     function list_show_query()
     {
         $data_query = Tower::where([['status', 0]]);
         $data_query->select([
             'id',
             'template_name',
             'template_content',
             'template_code', 'is_mandatory', 'default_spare_or_customer', 'attachments', 'created_at'
         ]);
         return $data_query;
     }
    public function indexing(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        // print_r($societies_id);die();
        if ($request->id > 0) {
            $existingRecord = Tower::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        // 'societies_id', 'tower_name',
        // 'status', 'created_by', 'updated_by'
        // if ($request->societies_id > 0) {
        //     $Record = MasterSociety::find($request->societies_id);
        //     if (!$Record) {
        //         $response['status'] = 400;
        //         $response['message'] = 'Record not found for the society ID.';
        //         return $this->sendError($response);
        //     }
        // }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            // 'societies_id'                          => 'required|integer|min:1',
            'tower_name'                                    => 'required|unique:towers,tower_name,' . $id . ',id,deleted_at,NULL|max:255',
            
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Tower created successfully." : "Tower updated successfully.";

            $ins_arr = [
                'societies_id'                        => $societies_id,
                'tower_name'                     => $request->tower_name,
                'updated_by'                           => auth()->id(),
            ];
            // $table->tinyInteger('is_renewal_plan')->default(1)->comment('0-No_Renewal,1-Renewal');
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = Tower::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = ['id' => $qry->id, 'societies_id' => 25, 
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
        $data_query = $this->list_show_query();
        $data_query->where([['id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular tower found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find tower.';
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
        $terms = Tower::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Tower::updateOrCreate(
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
