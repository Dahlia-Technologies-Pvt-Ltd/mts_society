<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Master\{MasterSubscription};

class MasterSubscriptionController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
    {
        $data_query = MasterSubscription::where([['status', 0]]);
        $data_query->select([
            'id',
            'subscription_plan',
            'price',
            'features', 'frequency', 'is_renewal_plan', 'created_at'
        ]);
        return $data_query;
    }
    public function indexing(Request $request)
    {
        $data_query = $this->list_show_query();
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('subscription_plan', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('price', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('features', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('frequency', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('is_renewal_plan', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "subscription_plan", "price", "features", "frequency"];
        return $this->commonpagination($request, $data_query, $fields);  //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->id > 0) {
            $existingRecord = MasterSubscription::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $validator = Validator::make($request->all(), [
            'subscription_plan'  => 'required|unique:master_subscriptions,subscription_plan,' . $id . ',id,deleted_at,NULL|max:255',
            'price' => 'required|numeric|min:1|regex:/^\d+(\.\d{1,2})?$/',
            'features'  => 'required',
            'frequency' => 'required|integer|min:1',
            
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "Subscription created successfully." : "Subscription updated successfully.";

            $ins_arr = [
                'subscription_plan'                        => $request->subscription_plan,
                'price'                     => $request->price,
                'frequency'                          =>  $request->frequency,
                'features'                         => $request->features,
                'is_renewal_plan'            => ($request->is_renewal_plan === '0') ? 0 : 1,
                'updated_by'                           => auth()->id(),
            ];
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = MasterSubscription::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = ['id' => $qry->id, 'subscription_plan' => $qry->subscription_plan, 
                'price' => $qry->price, 'frequency' => $qry->frequency,
                 'features' => $qry->features, 'is_renewal_plan' => $qry->is_renewal_plan];
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
            $response['message'] = 'Unable to save Subscription.';
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
    public function delete(Request $request)
    {
        $subs = MasterSubscription::find($request->id);
        if ($subs) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = MasterSubscription::updateOrCreate(
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
