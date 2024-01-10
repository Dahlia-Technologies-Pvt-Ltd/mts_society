<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class CitiestableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities =[
            ['country_code'=>'IN','name'=>'Mumbai','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'DL','state_code'=>'DL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bangalore','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Hyderabad','state_code'=>'TG','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ahmedabad','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Chennai','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kolkata','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Surat','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Pune','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jaipur','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Lucknow','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kanpur','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Nagpur','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Indore','state_code'=>'MP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Thane','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bhopal','state_code'=>'MP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Visakhapatnam','state_code'=>'AP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Pimpri & Chinchwad','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Patna','state_code'=>'BR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Vadodara','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ghaziabad','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ludhiana','state_code'=>'PB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Agra','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Nashik','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Faridabad','state_code'=>'HR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Meerut','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Rajkot','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kalyan & Dombivali','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Vasai Virar','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Varanasi','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Srinagar','state_code'=>'JK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Aurangabad','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Dhanbad','state_code'=>'JH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Amritsar','state_code'=>'PB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Navi Mumbai','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Allahabad','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ranchi','state_code'=>'JH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Haora','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Coimbatore','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jabalpur','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Gwalior','state_code'=>'MP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Vijayawada','state_code'=>'AP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jodhpur','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Madurai','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Raipur','state_code'=>'CT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kota','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Guwahati','state_code'=>'AS','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Chandigarh','state_code'=>'PB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Solapur','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Hubli and Dharwad','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bareilly','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Moradabad','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Gurgaon','state_code'=>'HR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Aligarh','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jalandhar','state_code'=>'PB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Tiruchirappalli','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bhubaneswar','state_code'=>'OR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Salem','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Mira and Bhayander','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Thiruvananthapuram','state_code'=>'KL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bhiwandi','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Saharanpur','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Gorakhpur','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Guntur','state_code'=>'AP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bikaner','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Amravati','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Noida','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jamshedpur','state_code'=>'JH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bhilai Nagar','state_code'=>'CT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Warangal','state_code'=>'TG','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Cuttack','state_code'=>'OR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Firozabad','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kochi','state_code'=>'KL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Bhavnagar','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Dehradun','state_code'=>'UT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Durgapur','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Asansol','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Nanded Waghala','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Kolapur','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ajmer','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Gulbarga','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jamnagar','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ujjain','state_code'=>'MP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Loni','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Siliguri','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jhansi','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ulhasnagar','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Nellore','state_code'=>'AP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jammu','state_code'=>'JK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Sangli Miraj Kupwad','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Belgaum','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Mangalore','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Ambattur','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Tirunelveli','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Malegoan','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Gaya','state_code'=>'BR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Jalgaon','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Udaipur','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'IN','name'=>'Maheshtala','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
            ['country_code'=>'US','name'=>'Alexander City','state_code'=>'AL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],

        ];
        
        $insertdata = [];
        foreach ($cities as $k => $row) {
            $country_code = $row['country_code'];
            unset($row['country_code']);

            $state_code = $row['state_code'];
            unset($row['state_code']);
  
            $country = Country::where('country_code', $country_code)->first();
            $state = State::where('state_code', $state_code)->first();

            $insertdata[$k] = $row;
            $insertdata[$k]['country_id'] = $country->id;
            $insertdata[$k]['state_id'] = $state->id;
        }
        City::insert($insertdata);
    }
}
