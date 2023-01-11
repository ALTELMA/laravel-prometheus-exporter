<?php

namespace Altelma\LaravelPrometheusExporter\Middleware;

use Altelma\LaravelPrometheusExporter\Traits\MetricTrait;
use Closure;
use Illuminate\Support\Facades\Route as RouteFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusLaravelMiddleware
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
        $matchedRoute = $this->getMatchedRoute($request);

        $start    = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $this->requestCountMetric($request->method(), $matchedRoute->uri(), $response->getStatusCode());
        $this->requestLatencyMetric($request->method(), $matchedRoute->uri(), $response->getStatusCode(), $duration);

        return $response;
    }

    public function getMatchedRoute(Request $request)
    {
        $routeCollection = RouteFacade::getRoutes();
        return $routeCollection->match($request);
    }
}
