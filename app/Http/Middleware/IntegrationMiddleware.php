<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class IntegrationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Header obrigatório
        $clientId = $request->header('X-Client-Id');
        if (!$clientId) {
            return response()->json([
                'error' => 'Missing required header X-Client-Id'
            ], 400);
        }


        // Correlation ID
        $correlationId = $request->header('X-Request-Id', (string) Str::uuid());
        $request->headers->set('X-Request-Id', $correlationId);


        // Rate limiting simples por cliente (opcional)
        $max = (int) (config('app.rate_limit_max', env('RATE_LIMIT_MAX', 120)));
        $decay = (int) (config('app.rate_limit_decay', env('RATE_LIMIT_DECAY_SECONDS', 60)));
        $key = sprintf('rate:%s', $clientId);
        $count = Cache::add($key, 0, $decay) ? 0 : Cache::increment($key);
        if ($count > $max) {
            return response()->json([
                'error' => 'Too Many Requests'
            ], 429, [
                'Retry-After' => $decay
            ]);
        }


        $start = microtime(true);


        // Log de entrada
        Log::info('request_in', [
            'correlation_id' => $correlationId,
            'client_id' => $clientId,
            'method' => $request->method(),
            'route' => $request->path(),
            'query' => $request->query(),
        ]);


        $response = $next($request);


        $durationMs = (int) ((microtime(true) - $start) * 1000);


        // Log de saída
        Log::info('request_out', [
            'correlation_id' => $correlationId,
            'client_id' => $clientId,
            'route' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
        ]);


        // Anexar header de duração
        $response->headers->set('X-Request-Id', $correlationId);
        $response->headers->set('X-Response-Time', $durationMs . 'ms');


        return $response;
    }
}
