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
        Log::info('Line 26 ' . DB::connection()->getDatabaseName());
        // Set the new database configuration for society_master
        $societyMasterConnectionName = 'sqlsrv';
       /* $config =  Config::set("database.connections.$societyMasterConnectionName", [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', ''),
            'port' => env('DB_PORT', ''),
            'database' => 'society_master',
            'username' => 'sa',
            'password' => 'sa@123',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
        ]);
        Config::set("database.connections.$societyMasterConnectionName", $config);
        Config::set('database.default', $societyMasterConnectionName);


        // Fetch the last record from the master_users table in society_master database
        $user_data = DB::connection($societyMasterConnectionName)->table('master_users')->latest()->first();
        //$user_data = MasterUser::on($societyMasterConnectionName)->latest()->first();

        $society_data = DB::connection($societyMasterConnectionName)->table('master_socities')->latest()->first();*/

        if(!isset($params['master_user_id']) && !isset($params['master_socities_id']) && !isset($params['databaseName']) && !isset($params['databasePassword'])){
            return false;
        }
        
        $master_user_qry = MasterUser::on($societyMasterConnectionName)->where('id',$params['master_user_id']);
        $master_society_qry = MasterSociety::on($societyMasterConnectionName)->where('id',$params['master_socities_id']);

        if ($master_user_qry->exists() && $master_society_qry->exists()) {
            $user_data = $master_user_qry->first();
            $society_data = $master_society_qry->first();

            // Set the new database configuration for childdb
            $childdbConnectionName = 'sqlsrvclone';
            Config::set("database.connections.$childdbConnectionName", [
                'driver' => 'sqlsrv',
                'host' => env('DB_HOST', ''),
                'port' => env('DB_PORT', ''),
                'database' => $params['databaseName'],
                'username' => $params['databaseName'],
                'password' => $params['databasePassword'],
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
            ]);

            Config::set('database.default', $childdbConnectionName);

            // Create a new User in the childdb database
            if ($user_data && $society_data) {
                // Create a new User in the user Table
                $newUser = new User();
                $newUser->master_socities_id = $society_data->id;
                $newUser->user_type_id = $user_data->usertype;
                $newUser->full_name = $user_data->name;
                $newUser->username = $user_data->username;
                $newUser->email = $user_data->email;
                $newUser->phone_number = $user_data->phone_number;
                $newUser->save();

                // Create a new Society in the society Table
                $newSociety = new Society();
                $newSociety->master_society_id = $society_data->id;
                $newSociety->society_unique_code = $society_data->society_unique_code;
                $newSociety->society_name = $society_data->society_name;
                $newSociety->save();
            }
            DB::disconnect();
            $societyMasterConnectionName = 'sqlsrv';
            $config =  Config::set("database.connections.$societyMasterConnectionName", [
                'driver' => 'sqlsrv',
                'host' => env('DB_HOST', ''),
                'port' => env('DB_PORT', ''),
                'database' => 'society_master',
                'username' => 'sa',
                'password' => 'sa@123',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
            ]);
            Config::set("database.connections.$societyMasterConnectionName", $config);
            Config::set('database.default', $societyMasterConnectionName);

            return true;
        }
        return false;
    }
}