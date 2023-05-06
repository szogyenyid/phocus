<?php

namespace Szogyenyid\Phocus;

use Exception;

/**
 * Exception thrown when a middleware fails.
 */
class MiddlewareFailedException extends Exception
{
    /**
     * The middleware class that failed.
     *
     * @var string
     */
    private string $middlewareClass;

    /**
     * Creates a new MiddlewareFailedException instance.
     *
     * @param string $middlewareClass The middleware class that failed.
     */
    public function __construct(string $middlewareClass)
    {
        $this->middlewareClass = $middlewareClass;
        parent::__construct("Middleware failed: " . $middlewareClass);
    }

    /**
     * Returns the middleware class that failed.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->middlewareClass;
    }
}
