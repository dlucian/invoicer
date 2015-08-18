<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
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
        if (!env('API_KEY'))
            die('APP_KEY not configured. Edit your .env file and set APP_KEY to something unique.');

        if ($request->input('key') != env('API_KEY') )
            return response()->json(['status' => 'fail', 'code' => 401, 'message' => '401 Unauthorized: Invalid key'], 401);

        return $next($request);
    }

}