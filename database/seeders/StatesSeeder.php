<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Master\State;
use App\Models\Master\Country;
use Illuminate\Support\Facades\DB;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries =[
          ['country_code'=>'IN','name'=>'Andaman and Nicobar Islands','state_code'=>'AN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Andhra Pradesh','state_code'=>'AP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Arunachal Pradesh','state_code'=>'AR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Assam','state_code'=>'AS','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Bihar','state_code'=>'BR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Chandigarh','state_code'=>'CH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Chhattisgarh','state_code'=>'CT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Dadra and Nagar Haveli','state_code'=>'DN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Daman and Diu','state_code'=>'DD','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Delhi','state_code'=>'DL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Goa','state_code'=>'GA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Gujarat','state_code'=>'GJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Haryana','state_code'=>'HR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Himachal Pradesh','state_code'=>'HP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Jammu and Kashmir','state_code'=>'JK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Jharkhand','state_code'=>'JH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Karnataka','state_code'=>'KA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Kerala','state_code'=>'KL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Lakshadweep','state_code'=>'LD','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Madhya Pradesh','state_code'=>'MP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Maharashtra','state_code'=>'MH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Manipur','state_code'=>'MN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Meghalaya','state_code'=>'ML','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Mizoram','state_code'=>'MZ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Nagaland','state_code'=>'NL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Odisha','state_code'=>'OR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Puducherry','state_code'=>'PY','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Punjab','state_code'=>'PB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Rajasthan','state_code'=>'RJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Sikkim','state_code'=>'SK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Tamil Nadu','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Telangana','state_code'=>'TG','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Tripura','state_code'=>'TR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Uttar Pradesh','state_code'=>'UP','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'Uttarakhand','state_code'=>'UT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'IN','name'=>'West Bengal','state_code'=>'WB','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Alabama','state_code'=>'AL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Alaska','state_code'=>'AK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Arizona','state_code'=>'AZ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Arkansas','state_code'=>'AR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'California','state_code'=>'CA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Colorado','state_code'=>'CO','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Connecticut','state_code'=>'CT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Delaware','state_code'=>'DE','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'District of Columbia','state_code'=>'DC','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Florida','state_code'=>'FL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Georgia','state_code'=>'GA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Hawaii','state_code'=>'HI','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Idaho','state_code'=>'ID','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Illinois','state_code'=>'IL','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Indiana','state_code'=>'IN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Iowa','state_code'=>'IA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Kansas','state_code'=>'KS','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Kentucky','state_code'=>'KY','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Louisiana','state_code'=>'LA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Maine','state_code'=>'ME','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Maryland','state_code'=>'MD','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Massachusetts','state_code'=>'MA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Michigan','state_code'=>'MI','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Minnesota','state_code'=>'MN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Mississippi','state_code'=>'MS','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Missouri','state_code'=>'MO','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Montana','state_code'=>'MT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Nebraska','state_code'=>'NE','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Nevada','state_code'=>'NV','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'New Hampshire','state_code'=>'NH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'New Jersey','state_code'=>'NJ','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'New Mexico','state_code'=>'NM','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'New York','state_code'=>'NY','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'North Carolina','state_code'=>'NC','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'North Dakota','state_code'=>'ND','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Ohio','state_code'=>'OH','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Oklahoma','state_code'=>'OK','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Oregon','state_code'=>'OR','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Pennsylvania','state_code'=>'PA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Rhode Island','state_code'=>'RI','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'South Carolina','state_code'=>'SC','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'South Dakota','state_code'=>'SD','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Tennessee','state_code'=>'TN','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Texas','state_code'=>'TX','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Utah','state_code'=>'UT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Vermont','state_code'=>'VT','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Virginia','state_code'=>'VA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Washington','state_code'=>'WA','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'West Virginia','state_code'=>'WV','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Wisconsin','state_code'=>'WI','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
          ['country_code'=>'US','name'=>'Wyoming','state_code'=>'WY','created_at' => Carbon::now()->format('Y-m-d H:i:s'),],
        ];

        $insertCountries = [];
        foreach ($countries as $k => $row) {
          $country_code = $row['country_code'];
          unset($row['country_code']);

          $country = Country::where('country_code', $country_code)->first();
          $insertCountries[$k] = $row;
          $insertCountries[$k]['country_id'] = $country->id;
        }
        
          State::insert($insertCountries);
    }
}

