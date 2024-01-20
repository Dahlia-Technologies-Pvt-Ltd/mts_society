<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CopyMasterSociety extends Model
{
    use HasFactory;

    /**
     * Convert master society data to society format.
     *
     * @param array $params
     * @return void
     */
    public static function masterToSociety($params = [])
    {
        // Set the new database configuration
        $connectionName = 'sqlsrvclone';
        $config = Config::get("database.connections.$connectionName", []);
        $config['database'] = $params['databaseName'];
        $config['username'] = $params['databaseName'];
        $config['password'] = $params['databasePassword'];
        $config['driver'] = 'sqlsrv';

        Config::set("database.connections.$connectionName", $config);
        Config::set('database.default', $connectionName);

        // Example: Insert data into the new database
        DB::table('your_table')->insert([
            'column1' => 'value1',
            'column2' => 'value2',
        ]);
    }
    /**
     * Convert master society to Master.
     *
     * @param array $params
     * @return void
     */
    public static function societyToMaster($params = [])
    {
        // Set the new database configuration
        $connectionName = 'sqlsrv';
        $config = Config::get("database.connections.$connectionName", []);
        $config['database'] = $params['databaseName'];
        $config['username'] = $params['databaseName'];
        $config['password'] = $params['databasePassword'];
        $config['driver'] = 'sqlsrv';

        Config::set("database.connections.$connectionName", $config);
        Config::set('database.default', $connectionName);
    }
}