<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
class DatabaseService
{
    public function __construct()
    {

    }

    // Create Database dynamically and run the migration
    public function createDatabase($params = [])
    {
        
        $databaseName = $params['dbname'];
        $databasePassword = $params['dbpassword'];

        DB::statement("CREATE DATABASE [$databaseName]");
        // Check if the login already exists
        $loginExists = DB::select("SELECT * FROM sys.sql_logins WHERE name = '$databaseName'");
        if (empty($loginExists)) {
            // If it doesn't exist, create the login
            DB::statement("CREATE LOGIN [$databaseName] WITH PASSWORD = '$databasePassword', CHECK_POLICY = OFF, CHECK_EXPIRATION = OFF");
        }

        Log::info('DB 31 ' . DB::connection()->getDatabaseName());

        // Create the user and grant privileges
        DB::statement("USE [$databaseName]");
        DB::statement("CREATE USER [$databaseName] FOR LOGIN [$databaseName]");
        DB::statement("ALTER ROLE [db_owner] ADD MEMBER [$databaseName]");

        // Set the new database configuration
        $connectionName = 'sqlsrv';
        $config = Config::get("database.connections.$connectionName", []);
        $config['database'] = $databaseName;
        $config['username'] = $databaseName;
        $config['password'] = $databasePassword;
        Config::set("database.connections.$connectionName", $config);
        Config::set('database.default', $connectionName);

        // Call the artisan command for migrating
        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--database' => $connectionName,
            '--path' => 'database/migrations/society_clone',
        ]);

        // Call the artisan command for seeding 
        //php artisan db:seed --class=CountriestableSeeder
        Artisan::call('db:seed', ['--class' => 'CountriestableSeeder']);
        Artisan::call('db:seed', ['--class' => 'StatesSeeder']);

        Log::info('DB 55 ' . DB::connection()->getDatabaseName());
        //Again connect back to Master DB
        DB::disconnect();
        DB::setDefaultConnection('sqlsrvmaster');
        Log::info('DB 59 ' . DB::connection()->getDatabaseName());

        return [
            'dbname' => $databaseName,
            'dbuser' => $databaseName,
            'dbpassword' => $databasePassword,
        ];
    }

}