<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class adminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(isset($request->username) && isset($request->phone) && isset($request->email) && isset($request->password)
         && isset($request->type))
         {
            $request->username = filter_var($request->username,FILTER_SANITIZE_STRING);
            $request->phone = filter_var($request->phone,FILTER_SANITIZE_NUMBER_INT);
            $request->email = filter_var($request->email,FILTER_SANITIZE_EMAIL);
            $request->password = filter_var($request->password,FILTER_SANITIZE_STRING);
            $request->type = filter_var($request->type,FILTER_SANITIZE_NUMBER_INT);
         }

         if(isset($request->username_user) && isset($request->id_user) && isset($request->phone) && isset($request->email))
         {
            $request->username_user = filter_var($request->username_user,FILTER_SANITIZE_STRING);
            $request->id_user = filter_var($request->id_user,FILTER_SANITIZE_NUMBER_INT);
            $request->phone = filter_var($request->phone,FILTER_SANITIZE_EMAIL);
            $request->email = filter_var($request->email,FILTER_SANITIZE_STRING);
         }
        return $next($request);
    }
}
