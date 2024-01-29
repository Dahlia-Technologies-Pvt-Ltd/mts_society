<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Master\{MasterUser, MasterSociety};

class MasterUserController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    function list_show_query()
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
        }])->where([['status', 0]]);
        // print_r($data_query);die();
        $data_query->select([
            'id',
            'name',
            'username',
            'user_code', 'email', 'phone_number',
            'country_id', 'state_id', 'city', 'zipcode', 'usertype', 'blocked_at',
            'profile_picture', 'created_at'
        ]);
        return $data_query;
    }
    public function indexing(Request $request)
    {
        $data_query = $this->list_show_query();
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
    public function store(Request $request)
    {
        $existing_prof_pic = null;
        if ($request->id > 0) {
            $existingRecord = MasterUser::find($request->id);
            if (!$existingRecord) {
                $response['status'] = 400;
                $response['message'] = 'Record not found for the provided ID.';
                return $this->sendError($response);
            }
            $existing_prof_pic = $existingRecord->toArray()['profile_picture'];
            //
        }
        $id = empty($request->id) ? 'NULL' : $request->id;
        $profile_pic = trim($request->profile_picture) == '' || trim($request->profile_picture) === null ? '' : '|image|mimes:jpeg,png,jpg|max:5120';
        $validator = Validator::make($request->all(), [
            'name'                          => 'required',
            'username'                      => 'required|unique:master_users,username,' . $id . ',id,deleted_at,NULL|max:255',
            'phone_number'                  => 'required|digits:10|unique:master_users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
            'email'                        => 'required|email|unique:master_users,email,' . $id . ',id,deleted_at,NULL|max:255',
            'password'                     => 'required',
            'usertype'                     => 'min:0|max:2',
            'country_id'                   => 'required|integer',
            'state_id'                     => 'required|integer',
            'city'                          => 'required|string',
            'zipcode'                      => 'integer',
            'profile_picture'              => $profile_pic,

        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message = empty($request->id) ? "User created successfully." : "User updated successfully.";
            $filepath = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = $id.'/'.time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads/user_profile_pic', $fileName);
                if (isset($request->old_image) && !empty($request->old_image && Storage::exists($request->old_image))) {
                    Storage::delete($request->old_image);
                }
            } else if (isset($request->old_image) && !empty($request->old_image)  && empty($request->profile_picture)) {
                $filepath =$request->old_image;
            } else {
                if ($existing_prof_pic != null || $existing_prof_pic != '') {
                    if (Storage::exists($existing_prof_pic[0])) {
                        unlink($existing_prof_pic[0]);
                    }
                }
            }
            $ins_arr = [
                'name'                        => $request->name,
                'username'                    => $request->username,
                'email'                       => $request->email,
                'password'                    => Hash::make($request->password),
                'phone_number'                => $request->phone_number,
                'master_society_ids'          => isset($request->master_society_ids) ? jsonEncodeIntArr([$request->master_society_ids]) : 0,
                'gender'                      => $request->gender,
                'address'                     => $request->address,
                'country_id'                  => $request->country_id,
                'state_id'                    => $request->state_id,
                'city'                          => $request->city,
                'zipcode'                     => $request->zipcode,
                'usertype'                    => isset($request->usertype) ? $request->usertype : 0,
                'blocked_at'                  => $request->blocked_at,
                'profile_picture'             => $filepath,
                'updated_by'                           => auth()->id(),
            ];
            if (empty($request->id)) {
                $obj = new MasterUser();
                $ins_arr['user_code'] = $obj->generateUserCode();
            }
            if (!$request->id) {
                $ins_arr['created_by'] = auth()->id();
            } else {
                $ins_arr['updated_by'] = auth()->id();
            }
            $qry = MasterUser::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
        }
        if (request()->is('api/*')) {
            if ($qry) {
                $response['status'] = 200;
                $response['message'] = $message;
                $response['data'] = [
                    'id' => $qry->id, 'name' => $qry->name,
                    'username' => $qry->username, 'user_code' => $qry->user_code,
                    'email' => $qry->email, 'phone_number' => $qry->phone_number,
                    'master_society_ids' => $qry->master_society_ids,
                    'gender' => $qry->gender, 'street_address' => $qry->street_address,
                    'country_id' => $qry->country_id, 'state_id' => $qry->state_id,
                    'city' => $qry->city, 'zipcode' => $qry->zipcode,
                    'usertype' => $qry->usertype, 'blocked_at' => $qry->blocked_at,
                    'profile_picture' => $qry->profile_picture
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
            $response['message'] = 'Unable to save User.';
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
            $message = "Particular user found";
            $response['message'] = $message;
            $response['data'] = $result;
            $response['status'] = 200;
            return $this->sendResponse($response); //Assigning a Value
        } else {
            $response['message'] = 'Unable to find user.';
            $response['status'] = 400;
            return $this->sendError($response);
        } //
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
        $users = MasterUser::find($request->id);
        if ($users) {
            $ins_arr['deleted_by'] = auth()->id();
            $qry = MasterUser::updateOrCreate(
                ['id' => $request->id],
                $ins_arr
            );
            $users->destroy($request->id);
            $message = "Record Deleted Successfully !";
        } else {
            $message = "Record Not Found !";
        }
        $response['message'] = $message;
        $response['status'] = 200;
        return $this->sendResponse($response);
    }
}
