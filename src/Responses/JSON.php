<?php

namespace Szogyenyid\Phocus\Responses;

use Szogyenyid\Phocus\Response;
use Exception;

/**
 * A JSON response
 */
class JSON extends Response
{
    /**
     * Creates a new JSON response
     *
     * @param array<mixed> $data   The data to send.
     * @param integer      $status The HTTP status code to send.
     * @throws Exception           Failed to encode data to JSON.
     */
    public function __construct(array $data, int $status = 200)
    {
        $out = json_encode($data);
        if ($out === false) {
            throw new Exception("Failed to encode data to JSON");
        }
        header("Content-Type: application/json");
        http_response_code($status);
        echo $out;
    }
}
