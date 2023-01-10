<?php

namespace Altelma\LaravelPrometheusExporter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class MetricsAuthenticateWithBasicAuth
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|ResponseFactory|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isAuthenticated($request)) {
            return $next($request);
        }

        return response('Unauthorized.', Response::HTTP_UNAUTHORIZED);
    }

    private function isAuthenticated(Request $request): bool
    {
        $username = config('prometheus.metrics_route_auth.basic_auth.username');
        $password = config('prometheus.metrics_route_auth.basic_auth.password');

        if ((!empty($request->header('php-auth-user')) && $request->header('php-auth-user') === $username)
            && (!empty($request->header('php-auth-pw')) && $request->header('php-auth-pw') === $password)
        ) {
            return true;
        }

        return false;
    }
}
