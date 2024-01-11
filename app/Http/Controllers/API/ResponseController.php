<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\MasterUser;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function sendResponse($response)
    {
		// Generate array response
		if(isset($response['data']) && !empty($response['data']))
		{
			$ReturnResponse = [
				'success' => true,
				'data'    => (!empty($response['data'])) ? $response['data'] : '',
				'message' => isset($response['message']) ? $response['message'] : '',
			];

            if(isset($response['extra_data']) && is_array($response['extra_data'])){
                $ReturnResponse = array_merge($ReturnResponse,$response['extra_data']);
            }
		}
		else
		{
			$ReturnResponse = [
				'success' => true,
				'message' => isset($response['message']) ? $response['message'] : '',
			];
		}
		// Return generated array response as Json
        return response()->json($ReturnResponse, $response['status']);
    }

    public function sendError($response)
    {
        // Generate array response
    	$ErrorResponse = [
            'success' => false,
            'message' => $response['message'],
            'validation_error' => isset($response['validation_error']) ? $response['validation_error'] : ''
        ];

        if(isset($response['extra_data']) && is_array($response['extra_data'])){
            $ErrorResponse = array_merge($ErrorResponse,$response['extra_data']);
        }		
		// Return generated array response as Json 
        return response()->json($ErrorResponse, $response['status']);
    }
    function commonpagination($request,$data_query,$fields = [],$params = [])
    {
        $sortBy = 'id';
        if(isset($request->sortBy) && !empty($request->sortBy)){
            $sortBy = $request->sortBy;
        }       
        if (!in_array($sortBy, $fields)) {
            $response['status'] = 400; // You can use an appropriate HTTP status code
            $response['message'] = 'Invalid sortBy parameter. Allowed values: ' . implode(', ', $fields);
            return $this->sendError($response); 
        }

        $sortOrder = isset($request->sortOrder) && in_array($request->sortOrder,['asc','desc'])?$request->sortOrder:'desc';
        
        $perPage = $request->perPage ?: 200;
        $page = $request->page ?: 1; 
        $data_query->orderBy($sortBy, $sortOrder);
        // echo $data_query;
        $data = $data_query->paginate($perPage, ['*'], 'page', $page);
        if ($data->isEmpty()) {
            // If no records match the keyword, send an error response
            $message = "No records found.";
            $response['status'] = 200;
			$response['message'] = $message;
			return $this->sendError($response);
        } else {
            // If records are found, send a success response
            $message = "List found successfully!";
            $response['message'] = $message;
            $response['data'] = $data;
            $response['status'] = 200;
            if(isset($params['extra_data']) && !empty($params['extra_data'])){
                $response['extra_data'] = $params['extra_data'];
            }
            return $this->sendResponse($response);
        }
    }

    public function sendFailedLoginResponse(Request $request)
    {
        // Load MasterUser from database
        $user = MasterUser::where('email',$request->email_id)->orWhere('username',$request->email_id)->orWhere('phone_number',$request->email_id)->first(); 
        // Check if user was successfully loaded or not
        // If so, override the default error message.
        if (empty($user)) {
            $response['status'] = 401;
			$response['message'] = 'Invalid User Name or Email';
			return $this->sendError($response);
        }
        // Check if user was successfully loaded, that the password matches
        // and status is not 0. If so, override the default error message.
        else if ($user && \Hash::check($request->password, $user->password)) {
            $message = 'You account is Inactive. Kindly contact your administrator.';
            if($user->status == 'Inactive'){
                $response['message'] = $message;
            } else if($user->status == 'Blocked'){
                $message = 'You account is Blocked. Kindly contact your administrator.';
            } else {
                $message = 'Something went wrong!!';
            }
            $response['status'] = 401;
			$response['message'] = $message;
			return $this->sendError($response);
        }
		// Check if user was successfully loaded, that the password mismatches
        // If so, override the default error message.
		else
		{
            $response['status'] = 401;
			$response['message'] = 'Invalid Password';
			return $this->sendError($response);
		}
    }

    function validatorError($validator)
    {
        $responseArr['message'] = 'Validation Error';
        $responseArr['status'] = 406;
        $responseArr['validation_error'] =  $validator->errors();
        return $this->sendError($responseArr);
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
        //
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
    public function destroy(string $id)
    {
        //
    }
}
