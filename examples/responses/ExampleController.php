<?php

namespace Szogyenyid\Phocus\Examples;

use Szogyenyid\Phocus\Response;

class ExampleController
{
    public static function viewResponse(): Response
    {
        return Response::view(
            new ParameterView('John', 'Doe', new User('John', 'Doe'))
        );
    }

    public static function htmlResponse(): Response
    {
        return Response::html('<h1>Hello World!</h1>');
    }

    public static function jsonResponse(): Response
    {
        return Response::json([
            "success" => true,
            'hello' => 'world'
        ]);
    }

    public static function redirectResponse(): Response
    {
        return Response::redirect('/profile');
    }
}
