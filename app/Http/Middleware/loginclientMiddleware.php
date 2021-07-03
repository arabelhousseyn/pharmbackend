<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class loginclientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $req, Closure $next)
    {
        if(isset($req->iden) && isset($req->pass))
        {
            $req->iden = filter_var($req->iden,FILTER_SANITIZE_STRING);
            $req->pass = filter_var($req->pass,FILTER_SANITIZE_STRING);
        }

        if(isset($req->username) && isset($req->password))
        {
            $req->username = filter_var($req->username,FILTER_SANITIZE_STRING);
            $req->password = filter_var($req->password,FILTER_SANITIZE_STRING);
        }
        return $next($req);
    }
}
