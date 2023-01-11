<?php

namespace Altelma\LaravelPrometheusExporter\Middleware;

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Symfony\Component\HttpFoundation\Request;

class PrometheusLumenMiddleware extends PrometheusLaravelMiddleware
{
    public function getMatchedRoute(Request $request): \Illuminate\Routing\Route
    {
        $routeCollection = new RouteCollection();
        $routes          = RouteFacade::getRoutes();

        foreach ($routes as $route) {
            $routeCollection->add(
                new \Illuminate\Routing\Route(
                    $route['method'],
                    $route['uri'],
                    $route['action']
                )
            );
        }

        return $routeCollection->match($request);
    }
}
