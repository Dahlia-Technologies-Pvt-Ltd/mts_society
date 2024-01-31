<?php
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\MasterSociety;

function jsonEncodeIntArr($arr = []){
 
    return !empty($arr) ? json_encode(array_map('intval',array_values($arr))) : NULL;
 
}
// function getsocietyid($header){
 
//     // $headerValue = $request->header('society_id');
//     $match=MasterSociety::where('id', Crypt::decryptString($header))->first();
//     // print_r($match->id);die();
//   return !empty($match)?$match->id:'no header passed';
// }

function getsocietyid($header) {
    try {
        if (empty($header)) {
            return 'No header value passed';
        }
        $decryptedId = Crypt::decryptString($header);
        $match = MasterSociety::where('id', $decryptedId)->first();
        return !empty($match) ? $match->id : 'No matching record found/No header passed';
    } catch (DecryptException $e) {
        // Handle decryption errors
        return 'Decryption error: ' . $e->getMessage();
    }   
}

/**
 * Echo Eloquent Sql Query With Bindings
 *
 * @param [type] $query
 * @param integer $exit
 * @return void
 */
function eq($query)
{
    /* $str = vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
    return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
    */
    return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
        $binding = addslashes($binding);
        return is_numeric($binding) ? $binding : "'{$binding}'";
    })->toArray());
   
}