<?php

use Szogyenyid\Phocus\Method;
use Szogyenyid\Phocus\Router;

(new Router())
    ->route([
        Method::GET => [
            "/" => __DIR__ . '/../../templates/main-page.php',
            '/profile' => [ProfileController::class, 'myProfile'],
            '/logout' => function () {
                unset($_SESSION);
            }
        ]
    ]);
