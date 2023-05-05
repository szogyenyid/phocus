<?php

namespace Szogyenyid\Phocus\Tests\Unit;

use Exception;
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
