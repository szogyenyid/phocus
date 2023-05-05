<?php

namespace Szogyenyid\Phocus;

use Exception;

/**
 * A simple router class which works with static methods as actions.
 */
class Router
{
    /**
     * The base directory of the application.
     *
     * @var string
     */
    private string $baseDir;

    /**
     * The base url of the application.
     *
     * @var string
     */
    private string $baseUrl;

    /**
     * Whether the request has been completed or not.
     *
     * @var boolean
     */
    private bool $completed = false;

    /**
     * Whether to remove the trailing slash from the url or not.
     *
     * @var boolean
     */
    private bool $removeTrailingSlash = false;

    /**
     * Creates a new instance of Router.
     * @throws Exception If the current working directory cannot be determined.
     */
    public function __construct()
    {
        $cwd = getcwd();
        // @codeCoverageIgnoreStart
        // It is impossible to programatically disable getcwd()
        if ($cwd === false) {
            throw new Exception("Could not get current working directory.");
        }
        // @codeCoverageIgnoreEnd
        $this->baseDir = $cwd;
        $this->baseUrl = '/';
    }

    /**
     * Undocumented function
     *
     * @param string        $urlBase     If you are going to deploy your app to example.com/project,
     *                                   then set this to 'project'.
     * @param array<string> $subdirRegex Regexes that are not handled as parts of the route.
     *                                   Useful if you have different directories for different environments.
     * @return self
     */
    public function withBaseDir(string $urlBase = "", array $subdirRegex = []): self
    {
        $suffix = [];
        preg_match('/' . implode('|', $subdirRegex) . '/', $_SERVER['REQUEST_URI'], $suffix);
        $this->baseUrl = '/' . $urlBase . (isset($suffix[0]) ? '/' . $suffix[0] : '');
        $this->baseUrl = str_replace('//', '/', $this->baseUrl);
        if ($this->baseUrl != '/') {
            $this->baseUrl = rtrim($this->baseUrl, '/');
        }
        return $this;
    }

    /**
     * If this method is called, all trailing slashes will be removed from the requested URL. This is useful to
     * guarantee that the same route is called regardless of whether the user adds a trailing slash or not.
     *
     * @return self
     */
    public function removeTrailingSlash(): self
    {
        $this->removeTrailingSlash = true;
        return $this;
    }

    /**
     * The main method to call, and match the routes with the request.
     *
     * @param array<array<string,callable|string>> $array          The routes to match.
     * @param callable|null                        $fallbackAction The action to call if no route is matched.
     * @return void
     * @throws Exception                                           Route nor fallback not found.
     */
    public function route(array $array, ?callable $fallbackAction = null): void
    {
        foreach ($array as $method => $routes) {
            $method = Method::from($method)->value;
            foreach ($routes as $route => $action) {
                if ($action instanceof RouteGroup) {
                    $this->route($this->collapse($method, $route, $action->getRoutes()));
                    continue;
                }
                if ($method === "ANY") {
                    $this->matchRoute($route, $action);
                }
                $this->matchMethod($method, $route, $action);
                if ($this->completed) {
                    return;
                }
            }
        }
        if (!empty($fallbackAction) && is_callable($fallbackAction)) {
            $fallbackAction();
            return;
        }
        if (!isset($action)) {
            throw new Exception("Route nor fallback not found.");
        }
        return;
    }

    /**
     * Calls an action if the request method is the set one and matches the pattern
     *
     * @param string          $method The request method to match.
     * @param string          $route  The route to match.
     * @param callable|string $action The action to execute.
     * @return void
     */
    private function matchMethod(string $method, string $route, callable|string $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == $method) {
            $this->matchRoute($route, $action);
        }
    }

    /**
     * Collapses the routes array to a single path.
     *
     * @param string                        $method Possible values: "get", "post", "any".
     * @param string                        $prefix The prefix to add to the route.
     * @param array<string,callable|string> $routes The routes to collapse.
     * @return array<string,array<string,callable|string>> The routes collapsed to a single path
     */
    private function collapse(string $method, string $prefix, array $routes): array
    {
        $toRet = [
            $method => []
        ];
        foreach ($routes as $gr => $gra) {
            $toRet[$method][$prefix . $gr] = $gra;
        }
        return $toRet;
    }

    /**
     * Gets the route and request parts.
     *
     * @param string $route The route to match.
     * @return array<array<string>>        The route and request parts.
     * @throws Exception                   URL sanitization failed.
     */
    private function getRouteAndRequestParts(string $route): array
    {
        // Route parts:   Array (                               [0] => hello [1] => $name )
        // Request parts: Array ( [0] => profile [1] => preprod [2] => hello [3] => Mario )
        $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        if (!$request_url) {
            throw new Exception("URL sanitization failed");
        }
        if ($this->baseUrl != '/') {
            $request_url = str_replace($this->baseUrl, '', $request_url);
        }
        $request_url = strtok($request_url, '?') ?: $request_url;
        if ($this->removeTrailingSlash) {
            $request_url = rtrim($request_url, '/');
        }
        $route_parts = explode('/', $route);
        $request_url_parts = explode('/', $request_url);
        array_shift($route_parts);
        array_shift($request_url_parts);
        return [$route_parts, $request_url_parts];
    }

    /**
     * Handles the route matching.
     *
     * @param string          $route  The route to match.
     * @param callable|string $action The action to execute.
     * @return void
     * @throws Exception     URL sanitization failed.
     */
    private function matchRoute(string $route, callable|string $action): void
    {
        list($route_parts, $request_url_parts) = $this->getRouteAndRequestParts($route);
        if (count($route_parts) != count($request_url_parts)) {
            return;
        }
        $parameters = [];
        $noOfRouteParts = count($route_parts);
        for ($i = 0; $i < $noOfRouteParts; $i++) {
            $route_part = $route_parts[$i];
            if (preg_match("/^[$]/", $route_part)) {
                if (!isset($request_url_parts[$i])) {
                    return;
                }
                $route_part = ltrim($route_part, '$');
                array_push($parameters, $request_url_parts[$i]);
                $$route_part = $request_url_parts[$i];
            } elseif ($route_parts[$i] != $request_url_parts[$i]) {
                return;
            }
        }
        $this->handleAction($action, ...$parameters);
        return;
    }

    /**
     * Handles an action. Either calls a function or includes a file.
     *
     * @param string|callable $action The action to execute.
     * @return void
     * @throws Exception             File to include not found.
     */
    private function handleAction(string|callable $action, ...$parameters): void
    {
        if (is_callable($action)) {
            $action(...$parameters);
            $this->completed = true;
            return;
        }
        if (!file_exists($this->baseDir . '/' . $action)) {
            throw new Exception("File not found: " . $this->baseDir . '/' . $action);
        }
        include_once($this->baseDir . '/' . $action);
        $this->completed = true;
        return;
    }
}
