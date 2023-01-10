<?php

return [
    'namespace' => env('PROMETHEUS_NAMESPACE', 'app'),
    'metrics_route_enabled' => env('PROMETHEUS_METRICS_ROUTE_ENABLED', true),
    'metrics_route_path' => env('PROMETHEUS_METRICS_ROUTE_PATH', 'metrics'),
    'metrics_route_auth_enabled' => env('PROMETHEUS_METRICS_ROUTE_AUTH_ENABLED', false),
    'metrics_route_auth' => [
        'basic_auth' => [
            'username' => env('PROMETHEUS_METRICS_ROUTE_AUTH_USERNAME'),
            'password' => env('PROMETHEUS_METRICS_ROUTE_AUTH_PASSWORD'),
        ]
    ],
    'storage_adapter' => env('PROMETHEUS_STORAGE_ADAPTER', 'memory'),
    'storage_adapters' => [
        'redis' => [
            'host' => env('PROMETHEUS_REDIS_HOST', 'localhost'),
            'port' => env('PROMETHEUS_REDIS_PORT', 6379),
            'database' => env('PROMETHEUS_REDIS_DATABASE', 0),
            'timeout' => env('PROMETHEUS_REDIS_TIMEOUT', 0.1),
            'read_timeout' => env('PROMETHEUS_REDIS_READ_TIMEOUT', 10),
            'persistent_connections' => env('PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS', false),
            'prefix' => env('PROMETHEUS_REDIS_PREFIX', 'PROMETHEUS_'),
            'prefix_dynamic' => env('PROMETHEUS_REDIS_PREFIX_DYNAMIC', true),
            'name' => env('PROMETHEUS_REDIS_NAME', env('APP_NAME')),
        ],
    ],
    'collect_full_sql_query' => env('PROMETHEUS_COLLECT_FULL_SQL_QUERY', true),
    'collectors' => [
        // \Your\ExporterClass::class,
    ],
    'routes_buckets' => null,
    'sql_buckets' => null,
    'guzzle_buckets' => null,
];
