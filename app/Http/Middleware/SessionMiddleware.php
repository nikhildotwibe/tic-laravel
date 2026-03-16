<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class SessionMiddleware
{
    protected array $except = [
        'api/user/login',
        'api/user/register',
        'api/user/roles',
        'api/settings/country',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        if (!in_array($request->path(), $this->except)) {
            if (!auth('sanctum')->check()) {
                return response()->json([
                    'success' => false,
                    'data' => 'NOT AUTHENTICATED',
                    'message' => "Please Login",
                ], 401);
            }
        }

        return $next($request);
    }
}
