<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin\{HdMasterCategories,Parking,User,Flat,Society,HdMasterCategoryResolversTable,NonResidentialUserDetail,MasterServiceProvider};
use App\Models\Master\{MasterSociety,MasterUser};
class HdMasterCategoriesController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = HdMasterCategories::join('hd_master_category_resolvers_table', 'hd_master_categories.id', '=', 'hd_master_category_resolvers_table.hd_master_category_id')
        ->join('users', 'users.id', '=', 'hd_master_category_resolvers_table.user_id');
        $data_query->select([
            'hd_master_categories.id AS id',
            'hd_master_categories.category_name AS category_name',
            'hd_master_categories.image AS image',
            'hd_master_categories.turn_around_days AS turn_around_days',
            'hd_master_category_resolvers_table.id AS category_resolver_id',
            'hd_master_category_resolvers_table.hd_master_category_id AS hd_master_category_id',
            'hd_master_category_resolvers_table.master_service_provider_ids AS master_service_provider_ids',
            'hd_master_category_resolvers_table.user_id AS user_id',
            'users.name',
            'users.phone_number',
            'users.user_code AS user_code',
            'users.email',
        ]);
        return $data_query;
    }
    public function index(Request $request)
    {
        $data_query = $this->list_show_query();
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('hd_master_categories.category_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('hd_master_categories.turn_around_days', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.phone_number', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "hd_master_categories.category_name", "hd_master_categories.turn_around_days","users.phone_number","users.name","users.email"];
        return $this->commonpagination($request, $data_query, $fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        if ($request->id > 0) {
            $existingRecord = HdMasterCategories::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        if(isset($request->master_service_provider_ids)){
            $providers_ids = json_decode($request->master_service_provider_ids, true);
            foreach ($providers_ids as $key => $providersidValue) {
                $existingprovider = MasterServiceProvider::
                    where('id', $providersidValue)
                    ->exists();
            
                if (!$existingprovider) {
                    $response['status'] = 400;
                    $response['message'] =' Provider doesnt exists.';
                    return $this->sendError($response);
                }
            }
        }
        if ($request->user_id > 0) {
            $existingRecordfm = NonResidentialUserDetail::join('users', 'users.id', '=', 'non_residential_user_details.user_id')->where('team_type','=',0)->where('users.id','=',$request->user_id)->first();
            if (!$existingRecordfm) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $image = trim($request->image) == '' || trim($request->image) === null ? '' : '|image|mimes:jpeg,png,jpg|max:5120';
        $validator = Validator::make($request->all(), [
            'category_name' =>  'required|unique:hd_master_categories,category_name,' . $id . ',id,deleted_at,NULL|max:255',
            'image'=>$image,
            'turn_around_days'=>'required',
            'user_id'=>'required',
            
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $filepath = NULL;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = $id . '/' . time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads/hd_category', $fileName);
            } 
            $message = empty($request->id) ? "Helpdesk category created successfully." : "Helpdesk category updated successfully.";
            $ins_arr = [
                'societies_id'                        => $societies_id,
                'category_name'                     => $request->category_name,
                'turn_around_days'                     => $request->turn_around_days,
                'image'                     => $filepath,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = HdMasterCategories::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            if($qry){
                $ins_arr2 = [
                    'hd_master_sub_category_id'                     => null,
                    'master_service_provider_ids'                     => isset($request->master_service_provider_ids)?jsonEncodeIntArr(json_decode($request->master_service_provider_ids)):jsonEncodeIntArr([]),
                    'user_id'                     => $request->user_id,
                    // 'updated_by'                           => auth()->id(),
                ];
                $qry2= HdMasterCategoryResolversTable::updateOrCreate(
                    ['hd_master_category_id' =>  $qry->id],
                    $ins_arr2
                );
                
            }
        }
        if (request()->is('api/*')) {
            $data_query = $this->list_show_query();
            $data_query->where('hd_master_categories.id', $qry->id);
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
            $response['message'] = 'Unable to save helpdesk category.';
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
        $data_query->where([['hd_master_categories.id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular helpdesk category found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find helpdesk category.';
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
        $subs = HdMasterCategories::find($request->id);
        if ($subs) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = HdMasterCategories::updateOrCreate(
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
