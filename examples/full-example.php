<?php

use Szogyenyid\Phocus\Middleware;
use Szogyenyid\Phocus\RouteGroup;
use Szogyenyid\Phocus\Router;

(new Router())
    ->withBaseDir("project", ["hotfix", "staging", "PRJCT-\d+"])
    ->registerMiddleware([
        'auth' => [AuthMiddleware::class],
        'admin' => [AdminMiddleware::class],
    ])
    ->route(
        [
            'GET' => [
                '/' => [ProfileController::class, 'myProfile'],
                '/admin|auth,admin' => [AdminController::class, 'panelPage'],
                '/login' => [LoginController::class, 'loginPage'],
                '/newpass/$email/$token' => [UserManagementController::class, 'newpassPage'],
                '/profile|auth' => new RouteGroup(
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
            'POST' => [],
            'ANY' => []
        ]
    );

// --------

class AuthMiddleware implements Middleware
{
    public function process(): bool
    {
        return true;
    }
}

class AdminMiddleware implements Middleware
{
    public function process(): bool
    {
        return false;
    }
}