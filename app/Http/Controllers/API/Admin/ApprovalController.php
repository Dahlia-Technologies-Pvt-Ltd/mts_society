<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\{Flat, Floor, User};
use App\Helpers\MailHelper;
use App\Models\Master\{MasterUser, MasterSociety, EmailTemplate};

class ApprovalController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */

    function list_show_query($societies_id)
    {
        $data_query = MasterUser::with(['country' => function ($query) {
            $query->select(
                'id',
                'name',
                'country_code',
                'country_code',
                'created_at'
            );
        }, 'state' => function ($query) {
            $query->select(
                'id',
                'name',
                'state_code',
                'country_id',
                'created_at'
            );
        }])->where([['status', 2]])->where([['usertype', 0]])
            ->whereJsonContains('master_society_ids', $societies_id);
        $data_query->select([
            'id',
            'name',
            'master_society_ids',
            'username',
            'user_code', 'email', 'phone_number',
            'country_id', 'state_id', 'city', 'zipcode', 'usertype', 'blocked_at',
            'profile_picture', 'created_at'
        ]);
        return $data_query;
    }
    public function index(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        $data_query = $this->list_show_query($societies_id);
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('username', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('user_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('usertype', 'LIKE', '%' . $keyword . '%');
            });
        }
        $fields = ["id", "username", "user_code", "email", "usertype"];
        return $this->commonpagination($request, $data_query, $fields);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function approval(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        $id = $request->id;
        if ($request->master_user_id > 0) {
            $existingRecord = MasterUser::find($request->master_user_id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the user ID.';
                return $this->sendError($response);
            }
        }
        $validator = Validator::make($request->all(), [
            'master_user_id' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $data_query = MasterUser::where([['id', $request->master_user_id]])->where([['status', 2]]);
            if ($data_query->exists()) {
                $data_query->select([
                    'id',
                    'name',
                    'username',
                    'user_code', 'email', 'phone_number',
                    'country_id', 'state_id', 'city', 'master_society_ids', 'zipcode', 'usertype', 'blocked_at',
                    'profile_picture'
                ]);
                $user_data_for_approval = $data_query->first();
                if ($user_data_for_approval->exists()) {
                    $user_status = MasterUser::find($user_data_for_approval->id);
                    if ($user_status->exists()) {
                        $user_status->status = 0; //approved
                        $user_status->save();
                        User::create([
                            'name'                        => $user_data_for_approval->name,
                            'username'                    => $user_data_for_approval->username,
                            'user_code'        => $user_data_for_approval->user_code,
                            'email'                       => $user_data_for_approval->email,
                            'master_user_id'                       => $user_data_for_approval->id,
                            'phone_number'                => $user_data_for_approval->phone_number,
                            'master_society_ids'          => $user_data_for_approval->master_society_ids,
                            'gender'                      => $user_data_for_approval->gender,
                            'address'                     => $user_data_for_approval->address,
                            'country_id'                  => $user_data_for_approval->country_id,
                            'state_id'                    => $user_data_for_approval->state_id,
                            'city'                          => $user_data_for_approval->city,
                            'zipcode'                     => $user_data_for_approval->zipcode,
                            'usertype'                    =>  0, //user
                            'status'                      => 0,
                            'blocked_at'                  => $user_data_for_approval->blocked_at,
                            'profile_picture'             => $user_data_for_approval->profile_picture,
                            'updated_by'                           => auth()->id()
                        ]);

                        try {
                            $TemplateData = array(
                                'EMAIL' => $user_data_for_approval->email,
                                'USER_NAME' => $user_data_for_approval->name,
                            );
                            MailHelper::sendMail('RESIDENT_USER_APPROVED', $TemplateData);
                        } catch (Exception $exp) {
                            $response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
                            $response['status'] = 503;
                            return $this->sendError($response);
                        }

                        $response['status'] = 200;
                        $response['message'] = 'User approved successfully.';
                        $response['data'] = $user_status->only(['id', 'username', 'name', 'user_code', 'email', 'usertype', 'status', 'phone_number', 'token', 'profile_picture']);
                        return $this->sendResponse($response);
                    } else {
                        $response['message'] = 'Unable to find user.';
                        $response['status'] = 400;
                        return $this->sendError($response);
                    }
                } else {
                    $response['status'] = 400;
                    $response['message'] = 'Invalid data';
                    return $this->sendError($response);
                }
            } else {
                $response['status'] = 400;
                $response['message'] = 'Invalid data';
                return $this->sendError($response);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $societies_id = getsocietyid($request->header('society_id'));
        $data_query = $this->list_show_query($societies_id);
        $id=$request->id;
        $data_query->where([['id', $id]]);
        if ($data_query->exists()) {
            $result = $data_query->first()->toArray();
            $message = "Particular non approved resident user found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find non approved resident user.';
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
    public function destroy(string $id)
    {
        //
    }
}
