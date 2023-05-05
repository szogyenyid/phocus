<?php

use Szogyenyid\Phocus\Method;
use Szogyenyid\Phocus\RouteGroup;
use Szogyenyid\Phocus\Router;

(new Router())
    ->withBaseDir("project", ["hotfix", "staging", "PRJCT-\d+"])
    ->route(
        [
            Method::GET => [
                '/' => [ProfileController::class, 'myProfile'],
                '/admin' => [AdminController::class, 'panelPage'],
                '/login' => [LoginController::class, 'loginPage'],
                '/newpass/$email/$token' => [UserManagementController::class, 'newpassPage'],
                '/profile' => new RouteGroup(
                    [
                        '' => [ProfileController::class, 'myProfile'],
                        '/$id' => [ProfileController::class, 'profilePage'],
                        '/settings' => new RouteGroup(
                            [
                                '' => [ProfileController::class, 'settingsPage'],
                                '/cv' => [ProfileController::class, 'cvSettings'],
                                '/details' => [ProfileController::class, 'settingDetails'],
                            ]
                        )
                    ]
                ),
                '/register' => [UserManagementController::class, 'registerPage'],
            ],
            Method::POST => [],
            Method::ANY => []
        ]
    );
