<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLogger
{
    /**
     * Handle an incoming request and log API activity
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Process the request
        $response = $next($request);
        
        // Calculate response time
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
        
        // Log API request
        Log::channel('daily')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $this->sanitizeRequestData($request->all()),
            'response_code' => $response->getStatusCode(),
            'response_time_ms' => $responseTime,
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        // Add custom headers
        $response->headers->set('X-Response-Time', $responseTime . 'ms');
        $response->headers->set('X-RateLimit-Limit', '60');
        
        return $response;
    }
    
    /**
     * Sanitize request data (remove sensitive information)
     */
    private function sanitizeRequestData(array $data): array
    {
        $sanitized = $data;
        
        // Remove or mask sensitive fields
        $sensitiveFields = ['password', 'api_key', 'token', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($sanitized[$field])) {
                $sanitized[$field] = '***REDACTED***';
            }
        }
        
        return $sanitized;
    }
}
