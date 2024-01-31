<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Config;
use App\Models\Master\{MasterSociety, MasterDatabase};
class ConnectSocietyDb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $society_token = ($request->header('society-id')) ? $request->header('society-id') : '';
        $master_society_id = Crypt::decryptString($society_token);
        // $master_society_id = 1; //$society_token >> decrypt token

        $master_society = MasterSociety::where([['master_socities.id', $master_society_id], ['status', 0]])->join('master_database', function($join){ $join->on('master_database.master_socities_id', '=', 'master_socities.id');});
        //Also we need to check subscription is valid and active - this check is pending

        if ($master_society->exists()) {
            $master_society_obj = $master_society->first();
            // Disconnect the current connection
            // print_r($master_society_obj->toArray());die();
            DB::disconnect();
            // Set the second database connection
            Config::set("database.connections.sqlsrv", [
                'driver'        => 'sqlsrv',
                'url'           => env('DATABASE_URL'),
                'host'          => env('DB_HOST', 'localhost'),
                'port'          => env('DB_PORT', ''),
                "database"      => $master_society_obj->databasename,
                "username"      => $master_society_obj->databaseuid,
                "password"      => $master_society_obj->databasepwd,
                'charset'       => 'utf8',
                'collation'     => 'utf8_unicode_ci',
                'prefix'        => '',
                'prefix_indexes' => true,
                'strict'        => true,
                'engine'        => null,
            ]); 
            //DB::reconnect('sqlsrv');
            DB::setDefaultConnection('sqlsrv');
        } else {
            DB::setDefaultConnection('sqlsrvmaster');
        }
        return $next($request);
    }
}
