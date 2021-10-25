<?php

namespace Smokills\Http;

// use Illuminate\Contracts\Events\Dispatcher;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Factory as BaseFactory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Smokills\Http\Client\Factory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->bind(
            BaseFactory::class,
            function ($app) {
                return new Factory($app->make(Dispatcher::class));
            }
        );
    }
}
