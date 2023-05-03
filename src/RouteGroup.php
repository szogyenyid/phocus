<?php

namespace Szogyenyid\Phocus;

/**
 * A group of routes. Can only be used with the Router class.
 */
class RouteGroup
{
    /**
     * The routes and corresponding actions in the group
     *
     * @var array<string,callable|string>
     */
    private array $routes;

    /**
     * Creates a new route group
     *
     * @param array<string,callable|string> $routes The routes and corresponding actions in the group.
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Returns the routes and corresponding actions in the group
     *
     * @return array<string,callable|string>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
