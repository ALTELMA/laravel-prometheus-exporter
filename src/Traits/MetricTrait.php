<?php

namespace Altelma\LaravelPrometheusExporter\Traits;

use Altelma\LaravelPrometheusExporter\PrometheusExporter;

trait MetricTrait
{
    private $prometheusExporter;
    public function __construct(PrometheusExporter $prometheusExporter)
    {
        $this->prometheusExporter = $prometheusExporter;
    }

    /**
     * @param string $routeName
     * @param string $method
     * @param int $status
     */
    public function requestCountMetric(string $method, string $routeName, int $status): void
    {
        $counter = $this->prometheusExporter->getOrRegisterCounter(
            'requests_total',
            'the number of http requests',
            [
                'route',
                'method',
                'status_code',
            ],
        );

        $counter->inc([
            $routeName,
            $method,
            $status,
        ]);
    }

    private function requestLatencyMetric(string $method, string $routeName, int $status, int $duration): void
    {
        $histogram = $this->prometheusExporter->getOrRegisterHistogram(
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
                $routeName,
                $method,
                $status,
            ]
        );
    }
}
