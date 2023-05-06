<?php

namespace Szogyenyid\Phocus\Tests\Feature;

use Szogyenyid\Phocus\Middleware;
use Szogyenyid\Phocus\MiddlewareFailedException;
use Szogyenyid\Phocus\RouteGroup;
use Szogyenyid\Phocus\Router;

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

it('routes with params', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/hello/world";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/hello/$name' => function ($name) {
                echo "Hello $name";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello world");
});

it('routes with params and query', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/hello/world?foo=bar";
    $_GET['foo'] = "bar";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/hello/$name' => function ($name) {
                echo "$name" . $_GET['foo'];
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("worldbar");
});

it('matches the correct request method', function () {
    $_SERVER['REQUEST_METHOD'] = "POST";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => function () {
                echo "foo";
            }
        ],
        'POST' => [
            '/test' => function () {
                echo "bar";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("bar");
});

it('includes a template file', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => "tests/assets/template.php"
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello World");
});

it('handles a base directory', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/my-project/hello";
    ob_start();
    (new Router())
    ->withBaseDir("my-project")
    ->route([
        'GET' => [
            '/hello' => function () {
                echo "Hello World";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello World");
});

it('handles an environment directory', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/PRJCT-1234/hello";
    ob_start();
    (new Router())
    ->withBaseDir('', ['PRJCT-\d+'])
    ->route([
        'GET' => [
            '/hello' => function () {
                echo "Hello World";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello World");
});

it('handles a base directory and an environment directory', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/my-project/PRJCT-1234/hello";
    ob_start();
    (new Router())
    ->withBaseDir("my-project", ['PRJCT-\d+'])
    ->route([
        'GET' => [
            '/hello' => function () {
                echo "Hello World";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("Hello World");
});

it('calls a static method', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => [TestController::class, 'foo']
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("foo");
});

it('calls an instance method with a parameter', function () {
    $_SERVER['REQUEST_METHOD'] = "POST";
    $_SERVER['REQUEST_URI'] = "/test/2";
    ob_start();
    (new Router())->route([
        'POST' => [
            '/test/$number' => [new TestController(), 'duplicate']
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("4");
});

it('handles RouteGroups', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test/bar";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => new RouteGroup([
                '/bar' => function () {
                    echo "foo";
                }
            ])
        ],
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("foo");
});

it('handles embedded RouteGroups', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test/foo/bar/baz";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => new RouteGroup([
                '/foo' => new RouteGroup([
                    '/bar/$name' => function (string $text) {
                        echo $text;
                    }
                ])
            ])
        ],
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("baz");
});

it('call the fallback action', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        /* no routes */
    ], function () {
        echo "404foo";
    });
    $result = ob_get_clean();
    expect($result)->toBe("404foo");
});

it('handles ANY routes', function () {
    $_SERVER['REQUEST_METHOD'] = "PUT";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        'ANY' => [
            '/test' => function () {
                echo "foo";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("foo");
});

it('matches the first route', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())->route([
        'GET' => [
            '/test' => function () {
                echo "foo";
            }
        ],
        'ANY' => [
            '/test' => function () {
                echo "bar";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("foo");
});

it('removes trailing slash if directed so', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test/";
    ob_start();
    (new Router())
    ->removeTrailingSlash()
    ->route([
        'GET' => [
            '/test' => function () {
                echo "foo";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("foo");
});

it('does not remove trailing slash by default', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test/";
    ob_start();
    (new Router())
    ->route([
        'GET' => [
            '/test' => function () {
                echo "foo";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("");
});

it('can handle multiple middleware', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    ob_start();
    (new Router())
    ->registerMiddleware([
        "mw1" => DummyTrueMiddleware::class,
        "mw2" => DummyTrueMiddleware::class,
        "mw3" => DummyFalseMiddleware::class,
    ])
    ->route([
        'GET' => [
            '/test|mw1,mw2' => function () {
                echo "baz";
            }
        ]
    ]);
    $result = ob_get_clean();
    expect($result)->toBe("baz");
});

it('dies if a middleware fails', function () {
    $_SERVER['REQUEST_METHOD'] = "GET";
    $_SERVER['REQUEST_URI'] = "/test";
    expect(fn() => (new Router())
    ->registerMiddleware([
        "mw1" => DummyTrueMiddleware::class,
        "mw2" => DummyFalseMiddleware::class,
        "mw3" => DummyTrueMiddleware::class,
    ])
    ->route([
        'GET' => [
            '/test|mw1,mw2,mw3' => function () {
                echo "baz";
            }
        ]
    ]))->toThrow(MiddlewareFailedException::class);
});

// -----

class TestController
{
    public static function foo()
    {
        echo "foo";
    }

    public function duplicate(int $number)
    {
        echo $number * 2;
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
