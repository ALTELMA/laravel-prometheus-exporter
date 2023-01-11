<?php

namespace Altelma\LaravelPrometheusExporter\Controllers;

use Altelma\LaravelPrometheusExporter\PrometheusExporter;
use Laravel\Lumen\Routing\Controller;
use Prometheus\RenderTextFormat;
use Symfony\Component\HttpFoundation\Response;

class LumenMetricsController extends Controller
{
    /**
     * @var PrometheusExporter
     */
    protected $prometheusExporter;

    /**
     * @param PrometheusExporter $prometheusExporter
     */
    public function __construct(PrometheusExporter $prometheusExporter)
    {
        $this->prometheusExporter = $prometheusExporter;
    }

    /**
     * GET /metrics
     *
     * The route path is configurable in the prometheus.metrics_route_path config var, or the
     * PROMETHEUS_METRICS_ROUTE_PATH env var.
     *
     * @return Response
     */
    public function getMetrics(): Response
    {
        $metrics = $this->prometheusExporter->export();

        $renderer = new RenderTextFormat();
        $result   = $renderer->render($metrics);

        return response($result, Response::HTTP_OK, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
