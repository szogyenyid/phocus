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
        if ($cwd === false) {
            throw new Exception("Could not get current working directory.");
        }
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
        $suffix = array();
        preg_match('/' . implode('|', $subdirRegex) . '/', $_SERVER['REQUEST_URI'], $suffix);
        $this->baseUrl = '/' . $urlBase . (isset($suffix[0]) ? '/' . $suffix[0] : '');
        $this->baseUrl = str_replace('//', '/', $this->baseUrl);
        return $this;
    }
    /**
     * If this method is called, all trailing slashes will be removed from the requested URL. This is useful to
     * guarantee that the same route is called regardless of whether the user adds a trailing slash or not.
     *
     * @return void
     */
    public function removeTrailingSlash(): void
    {
        $this->removeTrailingSlash = true;
    }
    /**
     * The main method to call, and match the routes with the request.
     *
     * @param array<array<string,mixed>> $array          The routes to match.
     * @param mixed                      $fallbackAction The action to call if no route is matched.
     * @return void
     * @throws Exception                                 Route nor fallback not found.
     */
    public function route(array $array, mixed $fallbackAction = null): void
    {
        foreach ($array as $method => $routes) {
            foreach ($routes as $route => $action) {
                if ($action instanceof RouteGroup) {
                    $this->route($this->collapse($method, $route, $action->getRoutes()));
                } elseif ($method === "get") {
                    $this->get($route, $action);
                } elseif ($method === "post") {
                    $this->post($route, $action);
                } elseif ($method === "put") {
                    $this->put($route, $action);
                } elseif ($method === "delete") {
                    $this->delete($route, $action);
                } elseif ($method === "patch") {
                    $this->patch($route, $action);
                } elseif ($method === "any") {
                    $this->any($route, $action);
                } else {
                    throw new Exception("Invalid request method: " . $method);
                }
                if ($this->completed) {
                    die();
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
     * Collapses the routes array to a single path.
     *
     * @param string                        $method Possible values: "get", "post", "any".
     * @param string                        $prefix The prefix to add to the route.
     * @param array<string,callable|string> $routes The routes to collapse.
     * @return array<string,array<string,callable|string>> The routes collapsed to a single path
     */
    private function collapse(string $method, string $prefix, array $routes): array
    {
        $toRet = array(
            $method => array()
        );
        foreach ($routes as $gr => $gra) {
            $toRet[$method][$prefix . $gr] = $gra;
        }
        return $toRet;
    }
    /**
     * Calls an action if the request method is GET and matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function get(string $route, mixed $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->matchRoute($route, $action);
        }
    }
    /**
     * Calls an action if the request method is POST and matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function post(string $route, mixed $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->matchRoute($route, $action);
        }
    }
    /**
     * Calls an action if the request method is PUT and matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function put(string $route, mixed $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $this->matchRoute($route, $action);
        }
    }
    /**
     * Calls an action if the request method is DELETE and matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function delete(string $route, mixed $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $this->matchRoute($route, $action);
        }
    }
    /**
     * Calls an action if the request method is PATCH and matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function patch(string $route, mixed $action): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $this->matchRoute($route, $action);
        }
    }
    /**
     * Calls an action if the request matches the pattern
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     */
    private function any(string $route, mixed $action): void
    {
        $this->matchRoute($route, $action);
    }
    /**
     * Handles the route matching.
     *
     * @param string $route  The route to match.
     * @param mixed  $action The action to execute.
     * @return void
     * @throws Exception     URL sanitization failed.
     */
    private function matchRoute(string $route, mixed $action): void
    {
        $ROOT = $this->baseDir;

        // (Route parts: Array ( [0] => hello [1] => $name )
        // Request parts: Array ( [0] => profile [1] => preprod [2] => hello [3] => Dani ) )
        $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        if (!$request_url) {
            throw new Exception("URL sanitization failed");
        }
        if ($this->baseUrl != '/') {
            $request_url = str_replace($this->baseUrl, '', $request_url);
        }
        $request_url = rtrim($request_url, '/');
        $request_url = strtok($request_url, '?') ?: $request_url;
        if ($this->removeTrailingSlash) {
            $request_url = rtrim($request_url, '/');
        }
        $route_parts = explode('/', $route);
        $request_url_parts = explode('/', $request_url);
        array_shift($route_parts);
        array_shift($request_url_parts);
        if ($route_parts[0] == '' && count($request_url_parts) == 0) {
            is_callable($action) ? $action() : include_once($ROOT . '/' . $action);
            $this->completed = true;
        }
        if (count($route_parts) != count($request_url_parts)) {
            return;
        }
        $parameters = [];
        for ($i = 0; $i < count($route_parts); $i++) {
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
        if (is_callable($action)) {
                $action(...$parameters);
        } else {
            include_once($ROOT . '/' . $action);
        }
        $this->completed = true;
    }
}
