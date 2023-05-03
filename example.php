<?php

use Szogyenyid\Phocus\RouteGroup;
use Szogyenyid\Phocus\Router;

(new Router())
    ->withBaseDir("project", ["hotfix", "staging", "PRJCT-\d+"])
    ->route(
        [
            "get" => [
                '/' => [ProfileController::class, 'myProfile'],
                '/admin' => [AdminController::class, 'panelPage'],
                '/login' => [LoginController::class, 'loginPage'],
                '/newpass/$email/$token' => [UserManagementController::class, 'newpassPage'],
                '/profile' => new RouteGroup(
                    [
                        '' => [ProfileController::class, 'profilePage'],
                        '/$id' => [ProfileController::class, 'profilePage'],
                        '/settings' => new RouteGroup(
                            [
                                '' => [ProfileController::class, 'settingsPage'],
                                '/cv' => [ProfileController::class, 'cvPage'],
                                '/details' => [ProfileController::class, 'detailsPage'],
                            ]
                        )
                    ]
                ),
                '/register' => [UserManagementController::class, 'registerPage'],
            ],
            "post" => [],
            "any" => []
        ]
    );
