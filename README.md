# Phocus
A lightweight router with one purpose: route. No dependencies, no mess.

------

## Why Phocus?

Sometimes you just need a simple router. Something that does not require you to install a whole framework, but still does the job. Phocus is a lightweight router with one purpose: route. No dependencies, no mess.

## Installation

Installation is the easiest via [Composer](https://getcomposer.org/):

```bash
$ composer require szogyenyid/phocus
```

or add it by hand to your `composer.json` file.

## Usage

Using Phocus is as simple as creating arrays.

```php
(new Router())
    ->route([
        Method::GET => [
            "/" => [HomeController::class, 'homePage'],
            '/profile' => [ProfileController::class, 'myProfile'],
        ]
    ]);
```

### Available request methods

Phocus can handle all the HTTP methods: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD`, and has an additional `ANY` method, which will handle any type of request.

Instead of using the as strings, it is recommended to user the built-in `Method` enum.

### Available handlers

Serving routes is possible with two types of handlers: `callable` and `string`.

Callable handlers are the easiest to use, as you can just pass a function or a method to the router. It may be used with class methods, in the form of `[ClassName::class, 'methodName']` for static methods, as `[new ClassName(), 'methodName']` for instance methods.

If a string is set as a handler, Phocus will try to include the file the string points to. This use should be avoided, but works well in easy cases, like showing static pages.

### Route parameters

Routes may contain parameters, as a simple example, id-s. These parameters can be accessed in the handler function as arguments.
To use a parameter in a route, use the `$` sign before the parameter name. The parameter name can contain any character except `/`.
    
```php
// routes.php

Method::GET => [
    '/profile/$id' => [ProfileController::class, 'profilePage'],
]

// ProfileController.php

class ProfileController {
    public static function profilePage(int $id)
    {
        // Do something with the id
    }
}
```

### Route groups

Route groups are a great way to group routes together. They can be used to set a common prefix for routes. They can be embedded to each other in multiple levels.

```php
Method::GET => [
    '/profile' => new RouteGroup(
        [
            '' => [ProfileController::class, 'myProfile'],
            '/$id' => [ProfileController::class, 'profilePage'],
            '/settings' => new RouteGroup(
                [
                    '' => [ProfileController::class, 'settingsPage'],
                    '/cv' => [ProfileController::class, 'cvSettings'],
                    '/details' => [ProfileController::class, 'settingDetails'],
                ]
            )
        ]
    )
],
```

In the above example, the route `/profile/settings/details` will be handled by the `settingDetails` method of the `ProfileController` class.

### Fallback action

A single fallback action may be provided to the router. This action will be called if no route matches the request. The fallback action - if set - must be callable, the most common use case is to use a closure, which returns a 404 page.

```php
(new Router())->route(
    [
        // no routes, so no request will match
    ],
    function () {
        return new Response(404, [], 'Not found');
    }
);
```

### Serving from different directories

There are several use cases when you want to serve routes from a directory other than the root. It can be done with the `withBaseDir` method. It accepts two parameters, a string for an always present base directory, and an array of strings for directories which may or may not be present. The latter may be useful if you serve different environments from different directories.

```php
(new Router())
    ->withBaseDir('my-project', ['hotfix', 'dev', 'staging', 'PRJCT-\d+'])
    ->route(/* routes */);
```

In the above example, your project is accessible through `example.com/my-project`, and other environments are accessible through `example.com/my-project/hotfix`, `example.com/my-project/PRJCT-1234` etc. In all these cases, the routes will be served by the same methods (altough, their version may differ). 

## Upgrading

Phocus follows [semantic versioning](https://semver.org/), which means breaking changes may occur between major releases. As the current highest version is V1, you do not need to worry about versions at this moment.

## License

Phocus is licensed under [MIT License](LICENSE).