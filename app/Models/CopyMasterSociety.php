<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\User;
use App\Models\Admin\Society;
use App\Models\Master\{MasterUser, MasterSociety};
use Illuminate\Support\Facades\Log;

class CopyMasterSociety extends Model
{
    use HasFactory;

    /**
     * Convert master society data to society format.
     *
     * @param array $params
     * @return bool
     */
    public static function masterToSociety($params = [])
    {
        Log::info('Line A26 ' . DB::connection()->getDatabaseName());
        if(!isset($params['master_user_id']) && !isset($params['master_socities_id']) && !isset($params['databaseName']) && !isset($params['databasePassword'])){
            return false;
        }
        DB::disconnect();
        DB::setDefaultConnection('sqlsrvmaster');
        $master_user_qry = MasterUser::where('id',$params['master_user_id']);       
        $master_society_qry = MasterSociety::where('id',$params['master_socities_id']);
        Log::info('Line A60 ' . DB::connection()->getDatabaseName());
        if ($master_user_qry->exists() && $master_society_qry->exists()) {            
            $master_user_data = $master_user_qry->first();
            $master_society_data = $master_society_qry->first();
            Log::info('master_user_data ' . json_encode($master_user_data->toArray()));
            Log::info('master_society_data ' . json_encode($master_society_data->toArray()));
           
            // Set the new database configuration for childdb          
            $connectionName = 'sqlsrv';
            $config = Config::get("database.connections.$connectionName", []);
            $config['database'] = $params['databaseName'];
            $config['username'] = $params['databaseName'];
            $config['password'] = $params['databasePassword'];
            Config::set("database.connections.$connectionName", $config);
            Config::set('database.default', $connectionName);

            // Create a new User in the childdb database
            if ($master_user_data && $master_society_data) {
                // Create a new User in the user Table
                $newUser = new User();
                $newUser->master_society_ids = jsonEncodeIntArr([$master_society_data->id]);
                $newUser->usertype = $master_user_data->usertype;
                $newUser->name = $master_user_data->name;
                $newUser->username = $master_user_data->username;
                $newUser->email = $master_user_data->email;
                $newUser->phone_number = $master_user_data->phone_number;
                $newUser->user_code = $master_user_data->user_code;    
                $newUser->country_id = $master_user_data->country_id;
                $newUser->state_id = $master_user_data->state_id;
                $newUser->save();

                // Create a new Society in the society Table
                $newSociety = new Society();
                $newSociety->master_society_id = $master_society_data->id;
                $newSociety->society_unique_code = $master_society_data->society_unique_code;
                $newSociety->society_name = $master_society_data->society_name;
                $newSociety->save();
            }
 
            DB::disconnect();
            DB::setDefaultConnection('sqlsrvmaster');
            Log::info('Line 114 ' . DB::connection()->getDatabaseName());
            return true;
        } else {
            Log::info(' In else  ' . DB::connection()->getDatabaseName()); exit;
        }
        return false;
    }
}