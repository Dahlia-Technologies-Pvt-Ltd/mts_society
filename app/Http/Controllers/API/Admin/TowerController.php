<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Tower;

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
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function destroy(Request $request)
    {
        $terms = Tower::find($request->id);
        if ($terms) {
            $ins_arr['deleted_by'] = auth()->id();
            // $filePath = storage_path('app/' . $terms->attachments);
            // if (file_exists($filePath) && $terms->attachments != '') {
            //     unlink($filePath);
            // }
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
