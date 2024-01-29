<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Master\{Setting};
class SettingsController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function list_show_query()
    {
        $data_query = Setting::orderBy('id', 'asc');
        return $data_query;
    }
    public function index(Request $request){
        $data_query = $this->list_show_query();
        if ($data_query->exists()) {
        $result = $data_query->first()->toArray();
        $message="Particular system settings found";
        $response['message'] =$message;
        $response['data'] =$result;
        $response['status'] = 200;
        return $this->sendResponse($response); //Assigning a Value
          }else{
            $response['message'] = 'Unable to find system settings.';
            $response['status'] = 200;
             return $this->sendError($response);
            } 
    }
    // public function index()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function updating(Request $request)
    {
        $mail_through_ip = trim($request->mail_through_ip) == ''||trim($request->mail_through_ip) ==='0'||trim($request->mail_through_ip) ===null? 'required|max:255':'' ;
        $validator = Validator::make($request->all(), [
            'mail_mailer' => 'required|max:255',
            'mail_host' => 'required|max:255',
            'mail_port' => 'required|max:255',
            'mail_username' => 'required',
            'mail_password' =>  $mail_through_ip,
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required',
            'mail_ssl_enable' => 'required',
            'mail_through_ip' =>'required',
            'support_email' => 'required|email',
        ]
        );
        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {

            $id = 0;
            $existingRecord = Setting::orderBy('id', 'desc')->first();
            if ($existingRecord) {
                $id = $existingRecord->id;
            }
            $message = "System settings updated successfully.";
            $ins_arr = [
                'mail_through_ip' => $request->mail_through_ip,
                'mail_mailer' => $request->mail_mailer,
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_from_address' => $request->mail_from_address,
                'mail_from_name' => $request->mail_from_name,
                'mail_ssl_enable' => $request->mail_ssl_enable,
                'support_email' => $request->support_email
            ];
            $qry = Setting::updateOrCreate(
                ['id' => $id],
                $ins_arr
            );
        }
        $updatedRecord = Setting::where('id', $id)
            ->first();
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = $updatedRecord;
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
            $response['message'] = 'Unable to save system settings.';
            $response['status'] = 400;
            return $this->sendError($response);
        } //
    }

    /**
     * Display the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
