<?php

namespace Szogyenyid\Phocus\Responses;

use Szogyenyid\Phocus\Response;

/**
 * A redirect response
 */
class Redirect extends Response
{
    /**
     * Creates a new redirect response
     *
     * @param string  $url    The URL to redirect to.
     * @param integer $status The HTTP status code to send.
     */
    public function __construct(string $url, int $status = 302)
    {
        header("Location: $url", true, $status);
    }
}
