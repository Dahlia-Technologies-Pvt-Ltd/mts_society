<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\{Flat, Floor};

class FlatController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = Flat::join('floors', 'floors.id', '=', 'flats.floor_id')->Leftjoin('wings', 'wings.id', '=', 'floors.wing_id')
            ->join('towers', 'towers.id', '=', 'floors.tower_id');
        $data_query->select([
            'flats.id AS id',
            'towers.tower_name AS tower_name',
            'wings.wings_name',
            'flats.flat_name AS flat_name',
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
                    ->orWhere('flats.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('flats.flat_name', 'LIKE', '%' . $keyword . '%')
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
            $message = empty($request->id) ? "Flat created successfully." : "Flat updated successfully.";
            if (empty($request->id)) {
                $validator = Validator::make($request->all(), [
                    'floor_id' => 'required|integer|min:1',
                    'flat_number_arr' => 'required|json',
                ]);
        
                if ($validator->fails()) {
                    return $this->validatorError($validator);
                }
                $flatNumbersArray = json_decode($request->flat_number_arr, true);
                foreach ($flatNumbersArray as $key => $flatValue) {
                    $towerId = Floor::find($request->floor_id)->tower_id;
                    $existingFlat = Flat::join('floors', 'floors.id', '=', 'flats.floor_id')
                        ->where('flat_name', $flatValue)
                        ->whereHas('floor', function ($query) use ($towerId) {
                            $query->where('tower_id', $towerId);
                        })
                        ->exists();
                
                    if ($existingFlat) {
                        // Handle the case where the flat number already exists for the selected tower
                        $response['status'] = 400;
                        $response['message'] = 'Flat number ' . $flatValue . ' already exists for the selected tower.';
                        return $this->sendError($response);
                    }
                
                    // Continue processing if the flat number is unique for the selected tower
                    // Your other logic here...
                }
                $flatNumbersArray = array_map(function ($value) {
                    return str_replace(' ', '', $value);
                }, $flatNumbersArray);
                if (count($flatNumbersArray) !== count(array_unique($flatNumbersArray))) {
                    $response['status'] = 400;
                    $response['message'] = 'Duplicate values of flats for a particular floor are not allowed.';
                    return $this->sendError($response);
                }
                if ($flatNumbersArray) {
                    $ins_arr = [];
                    foreach ($flatNumbersArray as $key => $flatValue) {
                        $ins_arr[] = ['flat_name' => $flatValue, 'floor_id' => $request->floor_id, 'created_by' => auth()->id()];
                    }
                    Flat::insert($ins_arr);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'floor_id' => 'required|integer|min:1',
                    'flat_number' => 'required', // You can add more validation rules as needed
                ]);
        
                if ($validator->fails()) {
                    return $this->validatorError($validator);
                }
                $flat = Flat::find($request->id);
                if ($flat) {
                    $existingFlat = Flat::where('flat_name', $request->flat_number)
                        ->where('floor_id', $request->floor_id)
                        ->where('id', '<>', $request->id)
                        ->first();
                    if ($existingFlat) {
                        $response['status'] = 400;
                        $response['message'] = 'Flat name must be unique for a particular floor.';
                        return $this->sendError($response);
                    }
                    $flat->update([
                        'flat_name' => $request->flat_number,
                        'floor_id' => $request->floor_id,
                        'updated_by' => auth()->id(),
                    ]);
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'Record not found for the provided ID.';
                    return $this->sendError($response);
                }
            // }
        }
        $data_query = $this->list_show_query();
        $data_query->where('floors.id', $request->floor_id);  // Simplified where condition
        $queryResult = $data_query->get();
        if (request()->is('api/*')) {
            if ($queryResult) {
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
            if ($queryResult) {
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
        $data_query = $this->list_show_query();
        $data_query->where([['flats.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular flat found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find flat.';
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
        $terms = Flat::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Flat::updateOrCreate(
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
