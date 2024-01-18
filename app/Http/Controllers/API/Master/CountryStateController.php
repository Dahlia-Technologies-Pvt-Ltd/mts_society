<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Master\{Country,State};

class CountryStateController extends ResponseController
{
    /**
     * Display a listing of the resource.
     */
    public function country(Request $request)
    {
        $data_query = Country::select([
            'id',
            'name',
            'country_code',
            'phone_code','created_at'
        ]);
        // $data_query->
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('country_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone_code', 'LIKE', '%' . $keyword . '%');
                    
            });
        }
        $fields = ["id", "name", "country_code", "phone_code"];
        return $this->commonpagination($request, $data_query, $fields);  
    }
    public function state(Request $request)
    {
        // $data_query = CustomerDetail::select(['contact_person_name', 'contact_person_country_code', 'contact_person_number', 'contact_person_work_location', 'contact_person_work_email', 'users.profile_picture'])->join('users', 'users.id', '=', 'customer_details.user_id')
        // ->where(['users.user_type' => 2, 'users.id' => $id]);
        // $data_query = State::Leftjoin('countries', 'countries.id', '=', 'states.country_id');

        $data_query = State::Join('countries AS country', 'country.id', '=', 'states.country_id');
        $data_query->select([
            'id',
            'name',
            'country.name',
           
        ]);
        print_r($data_query->get()->toArray());die();
        $data_query->select([
            'id',
            'id',
            'price',
            'features', 'frequency', 'is_renewal_plan', 'created_at'
        ]);
       
        // $data_query->
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('country_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone_code', 'LIKE', '%' . $keyword . '%');
                    
            });
        }
        $fields = ["id", "name", "country_code", "phone_code"];
        return $this->commonpagination($request, $data_query, $fields);
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
