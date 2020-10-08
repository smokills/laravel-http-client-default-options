# Laravel Http Client default options

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smokills/laravel-http-client-default-options.svg?style=flat-square)](https://packagist.org/packages/smokills/laravel-http-client-default-options)
[![Build Status](https://img.shields.io/travis/smokills/laravel-http-client-default-options/stable.svg?style=flat-square)](https://travis-ci.org/smokills/laravel-http-client-default-options?branch=stable)
[![Total Downloads](https://img.shields.io/packagist/dt/smokills/laravel-http-client-default-options.svg?style=flat-square)](https://packagist.org/packages/smokills/laravel-http-client-default-options)
[![License](https://img.shields.io/packagist/l/smokills/laravel-http-client-default-options.svg?style=flat-square)](https://packagist.org/packages/smokills/laravel-http-client-default-options)

Set default available options to the Laravel Http Client.

## Installation

Install the package via composer:

```bash
composer require smokills/laravel-http-client-default-options
```

### Laravel
In a Laravel environment, the package will be autoregistered thanks to the Laravel Package Auto-Discovery

### Lumen
If You would like use this package within a Lumen installation, you have to register the Service Provider in the `app/bootstrap.php`

```php
$app->register(Smokills\Http\ServiceProvider::class);
```

## Usage
You may define global options for the Http client in following way

``` php
// In a boot Service provider method (ex: the AppServiceProvider)

public function boot()
{
    ...

    Http::withDefaultOptions([
        'base_uri' => 'https://foo.com',
        'headers' => [
            'X-Bar-Header' => 'bar'
        ],
    ]);
}
```

From now on, all subsequent request will use the default options we have provided:

```php
// Somewhere in the code

/**
 * Since we have defined the base_uri as default option, we can simply make a
 * request using only the uri.
 */
$response = Http::get('/baz');
```

We can still continue add other options or helpers if we need:

```php
// Somewhere in the code

/**
 * The debug option and the basic auth will be used together the default options defined before.
 */
$response = Http::withOptions([
    'debug' => 'true'
])->withBasicAuth('username', 'password')->get('/baz');
```

If you need to remove several or even all of the the default options, in order to make other requests, you may use the `withoutDefaultOptions` method.

```php
// Remove all of the default options...
$response = Http::withoutDefaultOptions()->get('https://bar.com');

// Remove some of the global options
$response = Http::withoutDefaultOptions([
    'option', 'another-option'
])->get('https://bar.com');

// You can pass options to remove as arguments as well
$response = Http::withoutDefaultOptions('option', 'another-option')->get('https://bar.com');

// If you would like to remove deeply nested options, you may use the the dot notation syntax
$response = Http::withoutDefaultOptions('header.X-Some-Header')->get('https://bar.com');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email vito.famiglietti@gmail.com instead of using the issue tracker.

## Credits

- [Vito Famiglietti](https://github.com/smokills)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
