<?php

namespace Szogyenyid\Phocus\Tests;

use Szogyenyid\Phocus\Middleware;

class DummyTrueMiddleware implements Middleware
{
    public function process(): bool
    {
        return true;
    }
}
