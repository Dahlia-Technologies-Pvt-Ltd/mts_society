<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\{HdMasterSubCategories,HdMasterCategories,Parking,User,Flat,Society,HdMasterCategoryResolversTable,NonResidentialUserDetail,MasterServiceProvider};
use App\Models\Master\{MasterSociety,MasterUser};

class HdMasterSubCategoriesController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
   
     function list_show_query()
     {
         $data_query = HdMasterSubCategories::join('hd_master_categories', 'hd_master_categories.id', '=', 'hd_master_sub_categories.hd_master_category_id');
         $data_query->select([
             'hd_master_sub_categories.id AS id',
             'hd_master_sub_categories.sub_category_name AS sub_category_name',
             'hd_master_sub_categories.image AS image',
             'hd_master_sub_categories.hd_master_category_id AS hd_master_category_id',
             'hd_master_categories.category_name AS category_name'
         ]);
         return $data_query;
     }
     public function index(Request $request)
     {
         $data_query = $this->list_show_query();
         if (!empty($request->keyword)) {
             $keyword = $request->keyword;
             $data_query->where(function ($query) use ($keyword) {
                 $query->where('hd_master_sub_categories.sub_category_name', 'LIKE', '%' . $keyword . '%')
                     ->orWhere('hd_master_categories.category_name', 'LIKE', '%' . $keyword . '%');
             });
         }
         $fields = ["id", "hd_master_sub_categories.sub_category_name", "hd_master_categories.category_name"];
         return $this->commonpagination($request, $data_query, $fields);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        if ($request->id > 0) {
            $existingRecord = HdMasterSubCategories::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if ($request->hd_master_category_id > 0) {
            $existingRecordcat = HdMasterCategories::find($request->hd_master_category_id);
            if (!$existingRecordcat) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $image = trim($request->image) == '' || trim($request->image) === null ? '' : '|image|mimes:jpeg,png,jpg|max:5120';
        $validator = Validator::make($request->all(), [
            'hd_master_category_id' =>  'required',
            // 'sub_category_name' => 'required|unique:hd_master_sub_categories,sub_category_name,' . $id . ',id,deleted_at,NULL|max:255',
            'image'=>$image
            
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $filepath = NULL;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = $id . '/' . time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads/hd_subcategory', $fileName);
            } 
            $message = empty($request->id) ? "Helpdesk subcategory created successfully." : "Helpdesk category updated successfully.";
             //[hd_master_category_id]
    //   ,[sub_category_name]
    //   ,[image]
    //   ,[status]
    //   use HasFactory;
            $ins_arr = [
                'hd_master_category_id'                        => $request->hd_master_category_id,
                'sub_category_name'=>isset($request->sub_category_name)?jsonEncodeIntArr(json_decode($request->sub_category_name)):jsonEncodeIntArr([]),
                'image'                     => $filepath,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = HdMasterSubCategories::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            $data_query = $this->list_show_query();
            $data_query->where('hd_master_sub_categories.id', $qry->id);
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
            $response['message'] = 'Unable to save helpdesk subcategory.';
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
        $data_query->where([['hd_master_sub_categories.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular subcategory found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find subcategory.';
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
        $subs = HdMasterSubCategories::find($request->id);
        if ($subs) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = HdMasterSubCategories::updateOrCreate(
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
