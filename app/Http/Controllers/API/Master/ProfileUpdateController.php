<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Storage;
use App\Models\Master\{MasterUser, State, City};

class ProfileUpdateController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateuser(Request $request)
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
        }
        $id =  $request->id;
        $profile_pic = trim($request->profile_picture) == '' || trim($request->profile_picture) === null ? '' : '|image|mimes:jpeg,png,jpg|max:5120';
        $validator = Validator::make($request->all(), [
            'id'                          => 'required|integer|min:1',
            'name'                                    => 'required|unique:master_users,name,' . $id . ',id,deleted_at,NULL|max:255',
            'username'                                    => 'required|unique:master_users,username,' . $id . ',id,deleted_at,NULL|max:255',
            'phone_number'                  => 'required|digits:10|unique:master_users,phone_number,' . $id . ',id,deleted_at,NULL|max:255',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city' => 'required|integer',
            'profile_picture'              => $profile_pic
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message =  "Profile updated successfully.";
            $filepath = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = $id . '/' . time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads/user_profile_pic', $fileName);
                if (isset($request->old_image) && !empty($request->old_image && Storage::exists($request->old_image))) {
                    Storage::delete($request->old_image);
                }
            } else if (isset($request->old_image) && !empty($request->old_image)  && empty($request->profile_picture)) {
                $filepath = $request->old_image;
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
                'phone_number'                => $request->phone_number,
                'gender'                      => $request->gender,
                'address'                     => $request->address,
                'country_id'                  => $request->country_id,
                'state_id'                    => $request->state_id,
                'city'                     => $request->city,
                'zipcode'                     => $request->zipcode,
                'usertype'                    => isset($request->usertype) ? $request->usertype : 0,
                'profile_picture'             => $filepath,
            ];
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

    public function updateprofilepicture(Request $request)
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
        }
        $id =  $request->id;
        $profile_pic = trim($request->profile_picture) == '' || trim($request->profile_picture) === null ? '' : '|image|mimes:jpeg,png,jpg|max:5120';
        $validator = Validator::make($request->all(), [
            'id'                          => 'required|integer|min:1',
            'profile_picture'              => $profile_pic
        ]);

        if ($validator->fails()) {
            return $this->validatorError($validator);
        } else {
            $message =  "Profile picture updated successfully.";
            $filepath = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = $id . '/' . time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('uploads/user_profile_pic', $fileName);
                if (isset($request->old_image) && !empty($request->old_image && Storage::exists($request->old_image))) {
                    Storage::delete($request->old_image);
                }
            } else if (isset($request->old_image) && !empty($request->old_image)  && empty($request->profile_picture)) {
                $filepath = $request->old_image;
            } else {
                if ($existing_prof_pic != null || $existing_prof_pic != '') {
                    if (Storage::exists($existing_prof_pic[0])) {
                        unlink($existing_prof_pic[0]);
                    }
                }
            }

            $ins_arr = [
                'profile_picture'             => $filepath,
            ];
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
                    'id' => $qry->id,
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
            $response['message'] = 'Unable to update profile picture.';
            $response['status'] = 400;
            return $this->sendError($response);
        }
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
