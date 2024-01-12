<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Master\MasterSociety;


class MasterSocietySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obj = new MasterSociety();
        MasterSociety::create(
            [
                'society_unique_code'              => $obj->generateSocietyCode(),
                'society_name'       => 'Vipulgarden',
                'email'             => 'vipul@vipul.com',
                'phone_number'      => '9654145985',
                'address'            => 'vipul gardens khandagiri',
                'adress2'          => 'near khandagiri',
                'country_id'          => 101,
                'state_id'           => 12,
                'city_id'             => 5,
                'zipcode'              => 751010,
                'gst_number'          => 5123123,
                'pan_number'          => 5133133,
                'updated_at'       => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at'      => Carbon::now()->format('Y-m-d H:i:s')


            ]

        );
        MasterSociety::create([
            'society_unique_code'              => $obj->generateSocietyCode(),
            'society_name'       => 'Trident',
            'email'             => 'Trident@Trident.com',
            'phone_number'      => '9654145986',
            'address'            => 'rasulgarh 4222',
            'adress2'          => 'near esplanade',
            'country_id'          => 101,
            'state_id'           => 12,
            'city_id'             => 5,
            'zipcode'              => 751019,
            'gst_number'          => 51112131,
            'pan_number'          => 51212121,
            'updated_at'       => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at'      => Carbon::now()->format('Y-m-d H:i:s')


        ]);
    }
}
