<?php

namespace Smokills\Http;

use Smokills\Http\Client\Factory;
use Illuminate\Http\Client\Factory as BaseFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->bind(BaseFactory::class, function () {
            return new Factory;
        });
    }
}
