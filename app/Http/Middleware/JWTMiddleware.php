<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token / Authenticated'], 401);
        }

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

// Set the authenticated user on the request object
        $request->auth = $user;

        return $next($request);
    }
}
