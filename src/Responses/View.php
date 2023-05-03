<?php

namespace Szogyenyid\Phocus\Responses;

use Szogyenyid\Phocus\AbstractView;
use Szogyenyid\Phocus\Response;

/**
 * A view response.
 */
class View extends Response
{
    /**
     * Creates a new view response.
     *
     * @param AbstractView $view   The view to render.
     * @param integer      $status The HTTP status code to send.
     */
    public function __construct(AbstractView $view, int $status = 200)
    {
        header("Content-Type: text/html");
        http_response_code($status);
        $view->render();
    }
}
