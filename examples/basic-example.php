<?php

use Szogyenyid\Phocus\Router;

(new Router())
    ->route([
        'GET' => [
            "/" => __DIR__ . '/../../templates/main-page.php',
            '/profile' => [ProfileController::class, 'myProfile'],
            '/logout' => function () {
                unset($_SESSION);
            },
            '/admin' => [new Router(), 'panelPage'],
        ]
    ]);
