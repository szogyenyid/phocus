<?php

use Szogyenyid\Phocus\Router;

(new Router())
    ->route([
        'GET' => [
            "/" => 'templates/main-page.php',
            '/profile' => [ProfileController::class, 'myProfile'],
            '/logout' => function () {
                unset($_SESSION);
            },
            '/admin' => [new AdminController(), 'panelPage'],
        ]
    ]);
