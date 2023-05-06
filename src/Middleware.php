<?php

namespace Szogyenyid\Phocus;

/**
 * An interface to be implemented by all middleware classes.
 */
interface Middleware
{
    /**
     * Processes the request, and returns true if the request can continue.
     *
     * @return boolean
     */
    public function process(): bool;
}
