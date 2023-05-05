<?php

use Szogyenyid\Phocus\Router;

(new Router())
    ->route([
        "get" => [
            "/" => __DIR__ . '/../../templates/main-page.php',
            '/profile' => [ProfileController::class, 'myProfile'],
            '/logout' => function () {
                unset($_SESSION);
            }
        ]
    ]);
