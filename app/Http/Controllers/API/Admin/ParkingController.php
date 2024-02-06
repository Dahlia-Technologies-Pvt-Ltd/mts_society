<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\{Parking,Wing,Tower,Flat,Floor};

class ParkingController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = Parking::join('floors', 'floors.id', '=', 'parkings.floor_id')->Leftjoin('wings', 'wings.id', '=', 'parkings.wing_id')
            ->join('towers', 'towers.id', '=', 'parkings.tower_id');
        $data_query->select([
            'parkings.id AS id',
            'parking_area_number',
            'parking_type',
            'vehicle_type',
            // 'flats.id AS flat_id',
            'towers.tower_name AS tower_name',
            'wings.wings_name',
            // 'flats.flat_name AS flat_name',
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
                    ->orWhere('parkings.parking_area_number', 'LIKE', '%' . $keyword . '%')
                    //  ->orWhere('parkings.id', 'LIKE', '%' . $keyword . '%')
                    // ->orWhere('flats.flat_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('towers.tower_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('wings.wings_name', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id","parkings.parking_area_number","floors.floor_name", "towers.tower_name", "wings.wings_name"];
        return $this->commonpagination($request, $data_query, $fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        if ($request->id > 0) {
            $existingRecord = Parking::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if ($request->wing_id > 0) {
            $existingRecord = Wing::find($request->wing_id);
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
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if ($request->floor_id > 0) {
            $existingRecord = Floor::find($request->floor_id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }

        $id = empty($request->id) ? 'NULL' : $request->id;
            $message = empty($request->id) ? "Parking created successfully." : "Parking updated successfully.";
            if (empty($request->id)) {
                $validator = Validator::make($request->all(), [
                    'floor_id' => 'required|integer|min:1',
                    'wing_id' => 'required|integer|min:1',
                    'tower_id' => 'required|integer|min:1',
                    'vehicle_type' => 'required|integer|in:0,2,4',// 2-Wheeler,4-Wheeler,0-other vehichle
                    'parking_type' => 'required|integer|min:0|max:1',  // 0-Resident Parking,1-Visitors Parking
                    'parking_number_arr' => 'required|json',
                ]);
              
                
        
                if ($validator->fails()) {
                    return $this->validatorError($validator);
                }
                $parkingNumbersArray = json_decode($request->parking_number_arr, true);
                $flatNumbersArray = array_map(function ($value) {
                    return str_replace(' ', '', $value);
                }, $parkingNumbersArray);
                if (count($parkingNumbersArray) !== count(array_unique($parkingNumbersArray))) {
                    $response['status'] = 400;
                    $response['message'] = 'Duplicate values of parking for a particular floor are not allowed.';
                    return $this->sendError($response);
                }
                if ($parkingNumbersArray) {
                    $ins_arr = [];
          
                    foreach ($parkingNumbersArray as $key => $parking) {
                        $ins_arr[] = [ 
                        'societies_id'                        =>  $societies_id,
                        'parking_area_number'              => $parking,
                        'floor_id'                      => $request->floor_id,
                        'tower_id'                       => $request->tower_id,
                        'wing_id'                         => $request->wing_id,
                        'vehicle_type'    =>isset($request->vehicle_type)?$request->vehicle_type:0,
                        'parking_type'    =>in_array($request->parking_type, [0, 2, 4]) ? $request->parking_type : 2,
                        'created_by'                       => auth()->id()];
                    }
                    Parking::insert($ins_arr);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'floor_id' => 'required|integer|min:1',
                    'wing_id' => 'required|integer|min:1',
                    'tower_id' => 'required|integer|min:1',
                    'vehicle_type' => 'required|integer|in:0,2,4',// 2-Wheeler,4-Wheeler,0-other vehichle
                    'parking_type' => 'required|integer|min:0|max:1',  // 0-Resident Parking,1-Visitors Parking
                    'parking_number' => 'required', // You can add more validation rules as needed
                ]);
        
                if ($validator->fails()) {
                    return $this->validatorError($validator);
                }
                $parking = Parking::find($request->id);
                if ($parking) {
                    $existingFlat = Parking::where('parking_area_number', $request->parking_number)
                        ->where('floor_id', $request->floor_id)
                        ->where('tower_id', $request->tower_id)
                        ->where('wing_id', $request->wing_id)
                        ->where('id', '<>', $request->id)
                        ->first();
                    if ($existingFlat) {
                        $response['status'] = 400;
                        $response['message'] = 'Parking number must be unique for a must be unique for a particular floor.';
                        return $this->sendError($response);
                    }
                    $parking->update([
                        'parking_area_number' => $request->parking_number,
                        // 'parking_area_number'              => $parking,
                        'floor_id'                      => $request->floor_id,
                        'tower_id'                       => $request->tower_id,
                        'wing_id'                         => $request->wing_id,
                        'vehicle_type'    =>isset($request->vehicle_type)?$request->vehicle_type:0,
                        'parking_type'    =>in_array($request->parking_type, [0, 2, 4]) ? $request->parking_type : 2,
                        'updated_by'                   => auth()->id(),
                    ]);
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'Record not found for the provided ID.';
                    return $this->sendError($response);
                }}
                $data_query = $this->list_show_query();
                    $data_query->where('towers.id', $request->tower_id);
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
                    $response['message'] = 'Unable to save parking.';
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
        $data_query->where([['parkings.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular parking found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find parking.';
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
        $terms = Parking::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = Parking::updateOrCreate(
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
    public function parkingVehicleType()
    {
        $data_query = $this->list_show_query();
        $data_query->where([['parkings.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Parking types found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find config.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
    }
}
