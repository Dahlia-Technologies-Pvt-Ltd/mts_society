<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Master\MasterDatabase;

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
       // $data = MasterDatabase::get();
      //  $databases=$data->toArray();
        // $databases = [];
        $connection_name = 'sqlsrvclone';
        //php artisan app:run-all-migration
        $databases[] = [
         'databasename' => 'society_clone',
         'databaseuid' => 'sa',
         'databasepwd' => 'sa@123',
        ];
        foreach ($databases as $key => $row) {   
            $config = config('database.connections.'.$connection_name);        
            $config['database'] =   $row['databasename'];
            $config['username'] =   $row['databaseuid'];
            $config['password'] =   $row['databasepwd'];
            $config['driver'] = 'sqlsrv'; 
            config()->set('database.connections.' . $connection_name, $config);
            config()->set('database.default', $connection_name);
            // call the artisan command for each tenant
            echo "\n ".( $key+ 1 )." Running a migration for ".$row['databasename'];       
            Artisan::call('migrate:fresh', ['--force' => true,'--database' => $connection_name,'--path' => 'database/migrations/society_clone']);
}
            echo "\n\nAll migrations ran successfully!\n";
        }
}
