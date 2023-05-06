<?php

namespace Szogyenyid\Phocus\Tests\Unit;

use Szogyenyid\Phocus\MiddlewareFailedException;

it('can be instantiated', function () {
    $exception = new MiddlewareFailedException('test');
    expect($exception)->toBeInstanceOf(\Szogyenyid\Phocus\MiddlewareFailedException::class);
});

it('can return the class', function () {
    $exception = new MiddlewareFailedException('test');
    expect($exception->getClass())->toBe('test');
});
