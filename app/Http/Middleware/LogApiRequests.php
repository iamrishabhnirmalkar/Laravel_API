<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\ApiLog;



class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('health')) {
            return $next($request);
        }
        // Log the request after the response is sent
        $response = $next($request);

        try {
            ApiLog::create([
                'ip'         => $request->ip(),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'user_id'    => optional($request->user())->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API request', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        return $response;
    }
}
