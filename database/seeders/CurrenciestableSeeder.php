<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CurrenciestableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies =[
            [
                'name'            => 'Indian Rupee',
                'currency_code'   => 'INR',
                'number_code'     => 356,
                'notrate'         => 1.00,
                'isbasecurrency'  => 1,                
                'status'          => 0,
                'created_by'      => 1,
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                
                
            ],
            [
                'name'            => 'US Dollar',
                'currency_code'   => 'USD',
                'number_code'     => 840,
                'notrate'         => 79.45,
                'isbasecurrency'  => 0,                
                'status'          => 0,
                'created_by'      => 1,
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                
            ],
            [
                'name'            => 'Euro',
                'currency_code'   => 'EUR',
                'number_code'     => 978,
                'notrate'         => 83.50,
                'isbasecurrency'  => 0,                
                'status'          => 0,
                'created_by'      => 1,
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                
            ],
            [
                'name'            => 'Pound Sterling',
                'currency_code'   => 'GBP',
                'number_code'     => 826,
                'notrate'         => 96.45,
                'isbasecurrency'  => 0,                
                'status'          => 0,
                'created_by'      => 1,
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                
            ],

            [
                'name'            => 'UAE Dirham',
                'currency_code'   => 'AED',
                'number_code'     => 784,
                'notrate'         => 23.05,
                'isbasecurrency'  => 0,                
                'status'          => 0,
                'created_by'      => 1,
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s'),
                
            ]

        ];
       
        DB::table('master_currencies')->insert($currencies);
    }
    
}
