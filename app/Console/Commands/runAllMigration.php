<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class runAllMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-all-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 'driver' => 'sqlsrv',
        //     'url' => env('DATABASE_URL'),
        //     'host' => env('DB_HOST','LAPTOP-RLPE4VM9'),
        //     'port' => env('DB_PORT'),
        //     'database' => env('DB_DATABASE', 'society_master'),
        //     'username' => env('DB_USERNAME', 'sa'),
        //     'password' => env('DB_PASSWORD', 'sa@123'),
        //     'charset' => 'utf8',
        //     'collation' => 'utf8_unicode_ci',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        
        // foreach ($tenants as $tenant) {
        //     // Set the config for each $tenant 
            $config = config('database.connections.sqlsrv');
        
                $config['database'] ='society_master';
                $config['username'] ='sa';
                $config['password'] = 'sa@123';
                $config['driver'] = 'sqlsrv';
                $config['url'] = 'DATABASE_URL';
                $config['port'] = 1433;
                $config['host'] = 'LAPTOP-RLPE4VM9';
                // $config['password'] = 'sa@123';
        
                config()->set('database.connections.' . 'society_master', $config);
                config()->set('database.default', 'society_master');
        
        //     // Now that we have the correct database connection 
        //     // we can simply call the artisan command for each tenant
        Artisan::call('migrate:fresh', ['--force' => true,'--database' => 'society_master']);
        // }

        // Artisan::call('migrate:fresh', ['--force' => true,'--database' => 'society_master']);
    }
}
