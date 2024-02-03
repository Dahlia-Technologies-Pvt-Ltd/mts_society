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
             'tower_name', 'societies_id', 'created_at'
         ]);
         return $data_query;
     }
    public function index(Request $request)
    {
        $data_query = $this->list_show_query();
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('tower_name', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "tower_name"];
        return $this->commonpagination($request, $data_query, $fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        $sql = "SELECT id FROM societies WHERE master_society_id =  $societies_id";
        $results = DB::select($sql);
        $net_id = $results[0]->id;
        if ($request->id > 0) {
            $existingRecord = Tower::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'tower_name'                                    => 'required|unique:towers,tower_name,' . $id . ',id,deleted_at,NULL|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Tower created successfully." : "Tower updated successfully.";

            $ins_arr = [
                'societies_id'                        => $net_id,
                'tower_name'                     => $request->tower_name,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = Tower::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $wingsData = json_decode($request->wings, true);
            if ($wingsData) {
                $ins_arr = [];
                foreach ($wingsData as $wingIdentifier => $wingValue) {
                    $ins_arr[] = ['wings_name' => $wingValue, 'tower_id' => $qry->id];
                }
                Wing::insert($ins_arr);
            }
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $data_query = $this->list_show_query();
                $data_query->where([['id', $qry->id]]);
                $result = $data_query->get()->toArray();
                $response['data'] = $result;
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
        $data_query->with(['wing' => function ($query) {
            $query->select(
                'id',
                'wings_name',
                'tower_id',
                'created_at'
            );
        }])->where([['id', $id]]);
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
    public function destroy(Request $request)
    {
        $tower = Tower::find($request->id);
        if (!$tower) {
            $response['message'] = "Record Not Found !";
            $response['status'] = 404;
            return $this->sendResponse($response);
        }
        // Delete related Wings first
        $wingsDeleted = Wing::where('tower_id', $tower->id)->delete();
        if ($wingsDeleted) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Tower::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $tower->destroy($request->id);
            $message = "Record Deleted Successfully !";
        } else {
            $message = "Failed to delete record. Please try again.";
        }
        $response['message'] = $message;
        $response['status'] = 200;
        return $this->sendResponse($response);
    }
}