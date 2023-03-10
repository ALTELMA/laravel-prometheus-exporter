<?php

namespace Altelma\LaravelPrometheusExporter\Providers;

use Altelma\LaravelPrometheusExporter\Controllers\LaravelMetricsController;
use Altelma\LaravelPrometheusExporter\Controllers\LumenMetricsController;
use Altelma\LaravelPrometheusExporter\Middleware\MetricsAuthenticateWithBasicAuth;
use Altelma\LaravelPrometheusExporter\PrometheusExporter;
use Altelma\LaravelPrometheusExporter\StorageAdapterFactory;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $source = __DIR__ . '/../../config/prometheus.php';

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => base_path('config/prometheus.php')], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('prometheus');
        }

        $this->mergeConfigFrom($source, 'prometheus');

        $this->loadRoutes();
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/prometheus.php', 'prometheus');

        $this->app->singleton(PrometheusExporter::class, function ($app) {
            $adapter    = $app['prometheus.storage_adapter'];
            $prometheus = new CollectorRegistry($adapter, true);
            $exporter   = new PrometheusExporter(config('prometheus.namespace'), $prometheus);
            foreach (config('prometheus.collectors') as $collectorClass) {
                $collector = $this->app->make($collectorClass);
                $exporter->registerCollector($collector);
            }
            return $exporter;
        });

        $this->app->alias(PrometheusExporter::class, 'prometheus');

        $this->app->bind('prometheus.storage_adapter_factory', function () {
            return new StorageAdapterFactory();
        });

        $this->app->bind(Adapter::class, function ($app) {
            $factory = $app['prometheus.storage_adapter_factory'];
            $driver  = config('prometheus.storage_adapter');
            $configs = config('prometheus.storage_adapters');
            $config  = Arr::get($configs, $driver, []);

            return $factory->make($driver, $config);
        });

        $this->app->alias(Adapter::class, 'prometheus.storage_adapter');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'prometheus',
            'prometheus.storage_adapter',
            'prometheus.storage_adapter_factory',
        ];
    }

    private function loadRoutes()
    {
        if (! config('prometheus.metrics_route_enabled')) {
            return;
        }

        $router = $this->app['router'];

        if ($this->app instanceof LaravelApplication) {
            if (config('prometheus.metrics_route_auth_enabled')) {
                $router->aliasMiddleware('prometheus.auth.basic', MetricsAuthenticateWithBasicAuth::class);
                $router->get(
                    config('prometheus.metrics_route_path'),
                    LaravelMetricsController::class . '@getMetrics'
                )->name('metrics')->middleware(['prometheus.auth.basic']);
            } else {
                $router->get(
                    config('prometheus.metrics_route_path'),
                    LaravelMetricsController::class . '@getMetrics'
                )->name('metrics');
            }
        } else {
            $routeConfig = [
                'as'   => 'metrics',
                'uses' => LumenMetricsController::class . '@getMetrics',
            ];

            if (config('prometheus.metrics_route_auth_enabled')) {
                $this->app->routeMiddleware([
                    'prometheus.auth.basic' => MetricsAuthenticateWithBasicAuth::class,
                ]);
                $routeConfig['middleware'] = 'prometheus.auth.basic';
            }

            $router->get(
                config('prometheus.metrics_route_path'),
                $routeConfig
            );
        }
    }
}
