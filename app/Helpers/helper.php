<?php
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

function jsonEncodeIntArr($arr = []){
 
    return !empty($arr) ? json_encode(array_map('intval',array_values($arr))) : NULL;
 
}