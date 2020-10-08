<?php

namespace Smokills\Http\Tests;

use Orchestra\Testbench\TestCase;
use Smokills\Http\Client\Factory;

class HttpClientTest extends TestCase
{
    /**
     * @var \Illuminate\Http\Client\Factory
     */
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Factory;
    }

    public function test_that_default_options_will_be_applied()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        $this->factory->get('https://foo.com');

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_default_options_will_be_ignored()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        $this->factory->withoutDefaultOptions();

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_chain_additional_options_beside_default_options()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        $this->factory->withOptions([
            'headers' => [
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
        ])->get('https://foo.com');

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_we_can_chain_additional_methods_beside_default_options()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        $this->factory->withOptions([
            'headers' => [
                'X-Another-Custom-Header' => 'another-custom-value'
            ],
        ])->withBasicAuth('username', 'password')->get('https://foo.com');

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }

    public function test_that_we_can_ignore_a_default_option()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
            ],
        ]);

        $this->factory->withoutDefaultOptions('headers');

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_ignore_a_default_option_using_dot_notation()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
        ]);

        $this->factory->withoutDefaultOptions('headers.X-Another-Custom-Header');

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_we_can_bulk_ignore_default_options()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        $this->factory->withoutDefaultOptions(['headers', 'auth']);

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }

    public function test_that_we_can_bulk_ignore_default_options_with_dot_notation()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        $this->factory->withoutDefaultOptions(['headers.X-Custom-Header', 'auth']);

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('Authorization');
        });

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Another-Custom-Header', 'another-custom-value');
        });
    }

    public function test_that_macros_will_still_works()
    {
        $this->factory->fake();

        $this->factory->macro('addXCustomHeader', function () {
            return $this->withHeaders(['X-Custom-Header' => 'custom-value']);
        });

        $this->factory->addXCustomHeader()->get('https://foo.com');

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    public function test_that_we_can_still_use_the_client_without_default_options_set()
    {
        $this->factory->fake([
            '*' => $this->factory->response(),
        ]);

        $response = $this->factory->get('htpps://foo.com');

        $this->assertEquals(200, $response->status());
    }

    public function test_that_we_can_still_define_macros_without_default_options_set()
    {
        $this->factory->fake([
            '*' => $this->factory->response(),
        ]);

        $this->factory->macro('addXCustomHeader', function () {
            return $this->withHeaders(['X-Custom-Header' => 'custom-value']);
        });

        $response = $this->factory->addXCustomHeader()->get('https://foo.com');

        $this->factory->assertSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value');
        });

        $this->assertEquals(200, $response->status());
    }

    public function test_that_we_can_remove_default_options_using_arguments()
    {
        $this->factory->fake();

        $this->factory->withDefaultOptions([
            'headers' => [
                'X-Custom-Header' => 'custom-value',
                'X-Another-Custom-Header' => 'another-custom-value',
            ],
            'auth' => ['username', 'password'],
        ]);

        $this->factory->withoutDefaultOptions('headers', 'auth');

        $this->factory->get('https://foo.com');

        $this->factory->assertNotSent(function ($request) {
            return $request->hasHeader('X-Custom-Header', 'custom-value') &&
                $request->hasHeader('X-Another-Custom-Header', 'another-custom-value') &&
                $request->hasHeader('Authorization');
        });
    }
}
