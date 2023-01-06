<?php

namespace Altelma\Facades\LaravelPrometheusExporter;

use Illuminate\Support\Facades\Facade;

class PrometheusFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'prometheus';
    }
}
