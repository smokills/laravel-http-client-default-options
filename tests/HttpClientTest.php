<?php

namespace Smokills\Http\Tests;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

class HttpClientTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(Dispatcher::class, Event::fake());

        Http::fake();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Smokills\Http\ServiceProvider',
        ];
    }

    public function test_that_default_options_will_be_applied()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        Http::get('https://foo.com');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_default_options_will_be_ignored()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        Http::withoutDefaultOptions();

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_chain_additional_options_beside_default_options()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        Http::withOptions([
            'headers' => [
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
        ])->get('https://foo.com');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_we_can_chain_additional_methods_beside_default_options()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        Http::withOptions([
            'headers' => [
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
        ])->withBasicAuth('username', 'password')->get('https://foo.com');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }

    public function test_that_we_can_ignore_a_default_option()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        Http::withoutDefaultOptions('headers');

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_ignore_a_default_option_using_dot_notation()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
        ]);

        Http::withoutDefaultOptions('headers.X-Another-Custom-Header');

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_we_can_bulk_ignore_default_options()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        Http::withoutDefaultOptions(['headers', 'auth']);

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }

    public function test_that_we_can_bulk_ignore_default_options_with_dot_notation()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        Http::withoutDefaultOptions(['headers.X-Custom-Header', 'auth']);

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('Authorization');
        });

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_macros_will_still_works()
    {
        Http::macro('addXCustomHeader', function () {
            return $this->withHeaders(['X-Custom-Header' => 'custom-value']);
        });

        Http::addXCustomHeader()->get('https://foo.com');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_still_use_the_client_without_default_options_set()
    {
        Http::fake([
            '*' =>  Http::response(),
        ]);

        $response =  Http::get('htpps://foo.com');

        $this->assertEquals(200, $response->status());
    }

    public function test_that_we_can_still_define_macros_without_default_options_set()
    {
        Http::fake([
            '*' =>  Http::response(),
        ]);

        Http::macro('addXCustomHeader', function () {
            return $this->withHeaders(['X-Custom-Header' => 'custom-value']);
        });

        $response =  Http::addXCustomHeader()->get('https://foo.com');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });

        $this->assertEquals(200, $response->status());
    }

    public function test_that_we_can_remove_default_options_using_arguments()
    {
        Http::withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        Http::withoutDefaultOptions('headers', 'auth');

        Http::get('https://foo.com');

        Http::assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }

    public function test_that_events_are_fired()
    {
        Http::get('https://foo.com');

        Event::assertDispatched(RequestSending::class);
        Event::assertDispatched(ResponseReceived::class);
    }
}
