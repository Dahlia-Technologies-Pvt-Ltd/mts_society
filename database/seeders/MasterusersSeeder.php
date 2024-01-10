<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class MasterusersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('master_users')->insert([
            'name'              => 'Super Admin',
            'username'          => 'admin001',
            'email'             => 'superadmin@dahlia.com',           
            'phone_number'      => '9988998899',
            'gender'            => 'Male',
            'password'          => Hash::make('admin@123'),           
            'usertype'          =>2,
            'country'           =>101,
            'state'             =>12,
            'city'              =>5,
            'updated_at'       =>Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'      =>Carbon::now()->format('Y-m-d H:i:s')
            //'email_verified_at' =>Carbon::now()->format('Y-m-d H:i:s')

        ]);
    }
}
