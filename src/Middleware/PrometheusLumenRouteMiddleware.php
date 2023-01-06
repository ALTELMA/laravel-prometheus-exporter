<?php

namespace Altelma\LaravelPrometheusExporter\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class PrometheusLumenRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeCollection = Route::getRoutes();
        if (is_null($routeCollection) || !isset($routeCollection[1]['uri'])) {
            return $next($request);
        }

        $matchedRoute = $routeCollection[1]['uri'];

        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $exporter = app('prometheus');
        $histogram = $exporter->getOrRegisterHistogram(
            'response_time_seconds',
            'It observes response time.',
            [
                'method',
                'route',
                'status_code',
            ],
            config('prometheus.guzzle_buckets') ?? null
        );

        $histogram->observe(
            $duration,
            [
                $request->method(),
                $matchedRoute,
                $response->getStatusCode(),
            ]
        );

        return $response;
    }
}
