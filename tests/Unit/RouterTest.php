<?php

namespace Szogyenyid\Phocus\Tests\Unit;

use Exception;
use Szogyenyid\Phocus\Middleware;
use Szogyenyid\Phocus\Router;
use ValueError;

it('can be constructed', function () {
    $router = new Router();
    expect($router)->toBeInstanceOf(Router::class);
});

it('handles a set baseDir', function () {
    $router = (new Router())
        ->withBaseDir('/foo');
    expect($router)->toBeInstanceOf(Router::class);
});

it('fails on invalid request method', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())->route([
        'FOO' => [
            '/' => function () {
                echo "Hello World";
            }
        ]
    ]))->toThrow(ValueError::class);
});

it('throws exception if neither route nor fallback is found', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())->route([
    ]))->toThrow(Exception::class);
});

it('throws exception if sanitization fails', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "";
    expect(fn() => (new Router())->route([
        'GET' => [
            '' => function () {
                echo "Hello World";
            }
        ]
    ]))->toThrow(Exception::class);
});

it('throws exception if file to include not found', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())->route([
        'GET' => [
            '/' => 'foo.php'
        ]
    ]))->toThrow(Exception::class);
});

it('can register middleware', function () {
    $router = (new Router())
        ->registerMiddleware(["dummy" => DummyTrueMiddleware::class]);
    expect($router)->toBeInstanceOf(Router::class);
});

it('throws an exception if unregistered middleware is used', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())->route([
        'GET' => [
            '/|mw' => function () {
                echo "Hello World";
            }
        ]
    ]))->toThrow(Exception::class);
});

it('throws exception if a middleware does not implement the MW interface', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())
        ->registerMiddleware(["mw" => WrongMiddleware::class])
        ->route([
            'GET' => [
                '/|mw' => function () {
                    echo "Hello World";
                }
            ]
        ]))->toThrow(Exception::class);
});

it('throws exception if a middleware fails', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    expect(fn() => (new Router())
        ->registerMiddleware(["mw" => DummyFalseMiddleware::class])
        ->route([
            'GET' => [
                '/|mw' => function () {
                    echo "Hello World";
                }
            ]
        ]))->toThrow(Exception::class);
});

it('routes', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/' => function () {
                echo "Hello World";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello World");
});

// -----

class WrongMiddleware
{
    public function process(): bool
    {
        return true;
    }
}

class DummyTrueMiddleware implements Middleware
{
    public function process(): bool
    {
        return true;
    }
}

class DummyFalseMiddleware implements Middleware
{
    public function process(): bool
    {
        return false;
    }
}
