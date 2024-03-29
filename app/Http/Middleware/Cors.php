<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*return $next($request)
        ->header('Access-Control-Allow-Origin: *');
        ->header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        ->header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');*/

       /*if($request->server('HTTP_ORIGIN')) {
            $origin = $request->server('HTTP_ORIGIN');
            
            header('Access-Control-Allow-Origin: ' . $origin);
            //header('Access-Control-Allow-Methods:', '*');
            header('Access-Control-Allow-Headers: Origin, Content-Type');
            //header('Access-Control-Allow-Credentials:', true);

            return $next($request);*/
         ///

     return $next($request)
     ->header('Access-Control-Allow-Origin', '*')
     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE','OPTIONS');
    // ->header('Access-Control-Allow-Credentials', true)
    // ->header('Access-Control-Allow-Headers', 'Authorization,Content-Type');
    }
}
