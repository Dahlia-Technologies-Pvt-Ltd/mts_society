<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;


class SubscriptionplansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscription =[
            [
                'subscription_plan'     => 'Starter',
                'price'                 => '1000',
                'frequency'             => '1',
                'features'              => 'Excluding option to raise tickets',
                'is_renewal_plan'       => '1',
                'created_at'          => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'   => 1,
            ],
            [
                'subscription_plan'     => 'Economy',
                'price'                 => '2000',
                'frequency'             => '1',
                'features'              => 'Excluding option to raise tickets',
                'is_renewal_plan'       => '1',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by'   => 1,
            ],
            
            

        ];
        /*$insertdata = [];
        foreach ($roles as $k => $row) {
            $insertdata[$k] = $row;
        }*/

        DB::table('subscription_plans')->insert($subscription);
    }
    
}
