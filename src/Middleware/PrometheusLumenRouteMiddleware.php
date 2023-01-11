<?php

namespace Altelma\LaravelPrometheusExporter\Middleware;

use Altelma\LaravelPrometheusExporter\Traits\MetricTrait;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusLumenRouteMiddleware
{
    use MetricTrait;

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
        $route = $request->route();
        if (is_null($route) || ! isset($route[1]['uri'])) {
            return $next($request);
        }

        $matchedRoute = $route[1]['uri'];

        $start    = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $this->requestCountMetric($request->method(), $matchedRoute->uri(), $response->getStatusCode());
        $this->requestLatencyMetric($request->method(), $matchedRoute->uri(), $response->getStatusCode(), $duration);

        return $response;
    }
}
