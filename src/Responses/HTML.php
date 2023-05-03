<?php

namespace Szogyenyid\Phocus\Responses;

use Szogyenyid\Phocus\Response;

/**
 * An HTML response
 */
class HTML extends Response
{
    /**
     * Creates a new HTML response.
     *
     * @param string  $html   The HTML to send.
     * @param integer $status The HTTP status code to send.
     */
    public function __construct(string $html, int $status = 200)
    {
        header("Content-Type: text/html");
        http_response_code($status);
        echo $html;
    }
}
