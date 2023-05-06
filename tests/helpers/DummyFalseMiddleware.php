<?php

namespace Szogyenyid\Phocus\Tests;

use Szogyenyid\Phocus\Middleware;

class DummyFalseMiddleware implements Middleware
{
    public function process(): bool
    {
        return false;
    }
}
