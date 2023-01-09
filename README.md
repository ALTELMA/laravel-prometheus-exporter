# Laravel Prometheus Exporter

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/altelma/laravel-prometheus-exporter.svg?style=flat-square)](https://packagist.org/packages/altelma/laravel-prometheus-exporter)
[![Total Downloads](https://poser.pugx.org/ALTELMA/laravel-prometheus-exporter/d/total.svg)](https://packagist.org/packages/altelma/laravel-prometheus-exporter)

A prometheus exporter package for Laravel and Lumen.

This package is a wrapper bridging [promphp/prometheus_client_php](https://github.com/PromPHP/prometheus_client_php) into Laravel and Lumen.

However, This package get inspire from another package below
- [Superbalist/laravel-prometheus-exporter](https://github.com/Superbalist/laravel-prometheus-exporter)
- [arquivei/laravel-prometheus-exporter](https://github.com/arquivei/laravel-prometheus-exporter)
- [triadev/LaravelPrometheusExporter](https://github.com/triadev/LaravelPrometheusExporter)

Feel free to use above package instead, because this package just try to cover compatibility for previous version of Laravel and Lumen such as 6.x to 8.x, etc.
## Installation
Install the package via composer
```shell
composer require altelma/laravel-prometheus-exporter
```

If you are using Lumen need to register the service provider in `bootstrap/app.php`

```php
$app->register(Altelma\LaravelPrometheusExporter\PrometheusServiceProvider::class);
```

## Configuration
The package has a default configuration which uses the following environment variables.
```dotenv
PROMETHEUS_NAMESPACE=app

PROMETHEUS_METRICS_ROUTE_ENABLED=true
PROMETHEUS_METRICS_ROUTE_PATH=metrics
PROMETHEUS_METRICS_ROUTE_MIDDLEWARE=null
PROMETHEUS_COLLECT_FULL_SQL_QUERY=true
PROMETHEUS_STORAGE_ADAPTER=memory

PROMETHEUS_REDIS_HOST=localhost
PROMETHEUS_REDIS_PORT=6379
PROMETHEUS_REDIS_TIMEOUT=0.1
PROMETHEUS_REDIS_READ_TIMEOUT=10
PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS=0
PROMETHEUS_REDIS_PREFIX=PROMETHEUS_
```

### Metrics with multiple pods in k8s
If you want to expose metrics endpoint with application that running in k8s. It needs to store the same information with a single endpoint. So you need to add config like below
```dotenv
PROMETHEUS_REDIS_NAME="<your_custom_name>"
```

You need to ensure config set like this and this support `Redis` adapter only
```dotenv
PROMETHEUS_REDIS_PREFIX_DYNAMIC=true
PROMETHEUS_REDIS_PREFIX=<your_prefix_name>
PROMETHEUS_REDIS_NAME="<your_custom_name>"
```

## Metrics

The package allows you to observe metrics on the application routes. Metrics on request method, request path and status code.

### Laravel
In order to observe metrics in Laravel application routes (the time between a request and response), you should register the following middleware in your application's `app/Http/Kernel.php`:

```php
protected $middleware = [
    \Altelma\LaravelPrometheusExporter\Middleware\PrometheusLaravelMiddleware::class,
];
```

### Lumen 9.x
In order to observe metrics in Lumen application routes (the time between a request and response), you should register the following middleware in your application's `bootstrap/app.php`:
```php
$app->middleware([
    \Altelma\LaravelPrometheusExporter\Middleware\PrometheusLumenMiddleware::class,
]);
```

### Lumen 8.x and Lower
In Lumen 8.x and lower, cannot get route in global middleware just need to do in route middleware instead

```php
$app->routeMiddleware([
    'http.prometheus' => \Altelma\LaravelPrometheusExporter\Middleware\PrometheusLumenRouteMiddleware::class,
]);
```

## สนับสนุนผมได้นะ ☕
สวัสดีเพื่อนๆ ทุกคนนะครับ หากมีข้อเสนอแนะอะไร แนะนำมาได้นะครับ
นอกจากนี้ เพื่อนๆ สามารถแวะไปอ่านบทความของผมเพิ่มเติมได้ [ที่นี่](https://medium.com/@altelma) ครับ

## Bug Report
This package is not perfect right, but can be improve together. If you found bug or have any suggestion. Send that to me or new issue. Thank you to use it.