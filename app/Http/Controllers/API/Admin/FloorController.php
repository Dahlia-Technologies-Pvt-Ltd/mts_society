<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\{Floor, Tower};

class FloorController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = Floor::join('towers', 'towers.id', '=', 'floors.tower_id')
            ->Leftjoin('wings', 'wings.id', '=', 'floors.wing_id');
        $data_query->select([
            'floors.id AS id',
            'floors.floor_name AS floor_name',
            'towers.tower_name AS tower_name',
            'floors.tower_id',
            'wings.wings_name',
            'floors.wing_id',

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
                    ->orWhere('wings.wings_name', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "floors.floor_name", "towers.tower_name", "wings.wings_name"];
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
            $existingRecord = Floor::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if ($request->tower_id > 0) {
            $existingRecord = Tower::find($request->tower_id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Tower Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'floor_name'                                    => 'required|unique:floors,floor_name,' . $id . ',id,deleted_at,NULL|max:255',
            'tower_id'                                      => 'required|integer|min:1',
            'wing_id'                                      => 'integer|min:1',


        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Floor created successfully." : "Floor updated successfully.";

            $ins_arr = [
                'societies_id'                        => $net_id,
                'floor_name'                     => $request->floor_name,
                'tower_id'                     => $request->tower_id,
                'wing_id'                     => isset($request->wing_id) ? $request->wing_id : 1,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = Floor::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = [
                    'id' => $qry->id, 'floor_name' => $qry->floor_name
                ];
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
            $response['message'] = 'Unable to save floor.';
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
        $data_query->where([['floors.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular floor found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find floor.';
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
        $terms = Floor::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Floor::updateOrCreate(
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
