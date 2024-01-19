<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Master\MasterUser;
use App\Models\Master\MasterSociety;
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
        $obj = new MasterUser();
        MasterUser::create([
            'name'              => 'Super Admin',
            'user_code'       => $obj->generateUserCode(),
            'username'          => 'superadmin001',
            'email'             => 'superadmin@dahlia.com',
            'phone_number'      => '9988998899',
            'gender'            => 'Male',
            'password'          => Hash::make('admin@123'),
            'usertype'          => 2,
            'country_id'           => 101,
            'state_id'             => 12,
            'city'              => 5,
            'updated_at'       => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'      => Carbon::now()->format('Y-m-d H:i:s')


        ]);
        $data = MasterSociety::get();
        $data_query = $data->first()->toArray()['id'];
        MasterUser::create([
            'name'              => 'Admin',
            'user_code'       => $obj->generateUserCode(),
            'username'          => 'admin002',
            'email'             => 'admin@dahlia.com',
            'phone_number'      => '9988998898',
            'gender'            => 'Male',
            'master_society_ids' => jsonEncodeIntArr([$data_query]),
            'password'          => Hash::make('admin@123'),
            'usertype'          => 1,
            'country_id'           => 101,
            'state_id'             => 12,
            'city'              => 5,
            'updated_at'       => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'      => Carbon::now()->format('Y-m-d H:i:s')


        ]);
    }
}
