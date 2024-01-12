<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\MasterUser;
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
        // $ins_arr['society_unique_code'] = $obj->generateSocietyCode();
        
        MasterUser::create([
            'name'              => 'Super Admin',
            'user_code'       =>$obj->generateUserCode(),
            'username'          => 'admin001',
            'email'             => 'superadmin@dahlia.com',           
            'phone_number'      => '9988998899',
            'gender'            => 'Male',
            'password'          => Hash::make('admin@123'),           
            'usertype'          =>2,
            'country_id'           =>101,
            'state_id'             =>12,
            'city_id'              =>5,
            'updated_at'       =>Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'      =>Carbon::now()->format('Y-m-d H:i:s')
            //'email_verified_at' =>Carbon::now()->format('Y-m-d H:i:s')

        ]);
    }
}
