<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevelopmentToolsAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        $ip = $request->ip();

        if (in_array($ip, config('auth.development_tools_whitelist_ips'))) {
            return $next($request);
        }

        $token = \Cache::get('development_tools_token_'.$ip);

        if (!$token) {
            $token = $request->query('token');
        }

        if ($token !== config('auth.development_tools_token')) {
            abort(403);
        }

        \Cache::put('development_tools_token_'.$ip, $token, now()->addMinutes(5));

        return $next($request);
    }
}
