<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $method = $request->method();
        $url = $request->fullUrl();
        $userAgent = $request->userAgent();

        Log::channel('daily')->info('API Hit', [
            'ip' => $ip,
            'method' => $method,
            'url' => $url,
            'user_agent' => $userAgent,
            'user_id' => optional($request->user())->id,
        ]);

        return $next($request);
    }
}
