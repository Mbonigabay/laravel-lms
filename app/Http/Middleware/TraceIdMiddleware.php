<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ka4ivan\LaravelLogger\Facades\Llog;
use Symfony\Component\HttpFoundation\Response;

class TraceIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get trace ID from header or generate a new one
        $traceId = $request->header('X-Trace-Id') ?: (string) Str::uuid();

        // Share trace ID with the request context
        $request->attributes->set('trace_id', $traceId);

        // Add to log context
        Log::withContext([
            'trace_id' => $traceId,
        ]);

        // Log request start using Llog for dashboard visibility
        Llog::info('Request started: '.$request->method().' '.$request->fullUrl(), [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'trace_id' => $traceId,
        ]);

        $response = $next($request);

        // Add trace ID to the response header
        $response->headers->set('X-Trace-Id', $traceId);

        return $response;
    }
}
