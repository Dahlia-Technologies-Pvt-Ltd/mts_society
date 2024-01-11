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
        $databases = [
            [
                'database' => 'society_clone',
                'username' => 'sa',
                'password' => 'sa@123',
            ],
            [
                'database' => 'mts_sociey',
                'username' => 'sa',
                'password' => 'sa@123',
            ]
        ];
        $connection_name = 'sqlsrvclone';
        foreach ($databases as $key => $row) {
                //Set the config for each child database
                $config = config('database.connections.'.$connection_name);        
                $config['database'] =   $row['database'];
                $config['username'] =   $row['username'];
                $config['password'] =   $row['password'];
                $config['driver'] = 'sqlsrv';        
                config()->set('database.connections.' . $connection_name, $config);
                config()->set('database.default', $connection_name);
                // call the artisan command for each tenant
                echo "\n ".( $key+ 1 )." Running a migration for ".$row['database'];       
                Artisan::call('migrate:fresh', ['--force' => true,'--database' => $connection_name,'--path' => 'database/migrations/society_clone']);
        }
        echo "\n\nAll migrations ran successfully!\n";
    }
}
