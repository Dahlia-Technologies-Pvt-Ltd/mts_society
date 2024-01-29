<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\User;
use App\Models\Admin\Society;
use App\Models\Master\MasterUser;

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
        // Set the new database configuration for society_master
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

        // Fetch the last record from the master_users table in society_master database
        $lastMasterUser = DB::connection($societyMasterConnectionName)->table('master_users')->latest()->first();
        //$lastMasterUser = MasterUser::on($societyMasterConnectionName)->latest()->first();

        $lastMasterSociety = DB::connection($societyMasterConnectionName)->table('master_socities')->latest()->first();

        if ($lastMasterUser) {
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
            if ($lastMasterUser && $lastMasterSociety) {
                // Create a new User in the user Table
                $newUser = new User();
                $newUser->master_socities_id = $lastMasterSociety->id;
                $newUser->user_type_id = $lastMasterUser->usertype;
                $newUser->full_name = $lastMasterUser->name;
                $newUser->username = $lastMasterUser->username;
                $newUser->email = $lastMasterUser->email;
                $newUser->phone_number = $lastMasterUser->phone_number;
                $newUser->save();

                // Create a new Society in the society Table
                $newSociety = new Society();
                $newSociety->master_society_id = $lastMasterSociety->id;
                $newSociety->society_unique_code = $lastMasterSociety->society_unique_code;
                $newSociety->society_name = $lastMasterSociety->society_name;
                $newSociety->save();
            }
            return true;
        }
        return false;
    }
}