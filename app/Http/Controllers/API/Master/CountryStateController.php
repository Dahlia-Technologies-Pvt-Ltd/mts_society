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
        $data_query = State::Leftjoin('countries', 'countries.id', '=', 'states.country_id');
        $data_query->select([
            'states.id AS id',
            'states.name AS state_name',
            'countries.name AS country_name',
            'states.state_code',
            'states.country_id',
            'countries.country_code',
            'countries.phone_code',
        ])->when(!empty($request->country_id), function ($query) use ($request) {
            return $query->where('states.country_id', $request->country_id);
        });        
        if (!empty($request->keyword)) {
            $keyword = $request->keyword;
            $data_query->where(function ($query) use ($keyword) {
                $query->where('states.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('state_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('countries.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('country_code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone_code', 'LIKE', '%' . $keyword . '%');
                    
            });
        }
        $fields = ["id", "states.name", "state_code","countries.name","country_code", "phone_code"];
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
