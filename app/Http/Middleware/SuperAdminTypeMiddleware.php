<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminTypeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->usertype == 2) {
            return $next($request);
        }else{
            $ReturnResponse = [
				'success' => false,
				'message' => 'You are not authorized for this request',
			];
            return response()->json($ReturnResponse, 401);
        }
    }
}
