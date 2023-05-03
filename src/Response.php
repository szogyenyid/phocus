<?php

namespace Szogyenyid\Phocus;

use Szogyenyid\Phocus\Responses\HTML;
use Szogyenyid\Phocus\Responses\JSON;
use Szogyenyid\Phocus\Responses\Redirect;
use Szogyenyid\Phocus\Responses\View;

/**
 * A HTTP response base class. This class is not meant to be instantiated.
 */
abstract class Response
{
    /**
     * Creates a new JSON response
     *
     * @param array<mixed> $data   The data to send.
     * @param integer      $status The HTTP status code to send.
     * @return JSON The response
     */
    public static function json(array $data, int $status = 200): JSON
    {
        return new JSON($data, $status);
    }

    /**
     * Creates a new HTML response.
     *
     * @param string  $html   The HTML to send.
     * @param integer $status The HTTP status code to send.
     * @return HTML The response
     */
    public static function html(string $html, int $status = 200): HTML
    {
        return new HTML($html, $status);
    }

    /**
     * Creates a new view response.
     *
     * @param AbstractView $view   The view to render.
     * @param integer      $status The HTTP status code to send.
     * @return View The response
     */
    public static function view(AbstractView $view, int $status = 200): View
    {
        return new View($view, $status);
    }

    /**
     * Creates a new redirect response
     *
     * @param string  $url    The URL to redirect to.
     * @param integer $status The HTTP status code to send.
     * @return Redirect The response
     */
    public static function redirect(string $url, int $status = 302): Redirect
    {
        return new Redirect($url, $status);
    }
}
