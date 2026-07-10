<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateWithCookie
{
    public function handle(Request $request, Closure $next)
    {
        // If Authorization header is already set, skip
        if ($request->bearerToken()) {
            return $next($request);
        }

        // Extract access_token from httpOnly cookie and set as Bearer token
        $token = $request->cookie('access_token');

        if ($token) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
