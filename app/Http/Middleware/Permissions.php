<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        // Pre-Middleware Action
        if($request->user()->hasPermission($permission)){
          $response = $next($request);
          return $response;
        }



        // Post-Middleware Action

        return response()->json(['message'=>'user not authorised to do the action','action'=>$permission]);
    }
}
