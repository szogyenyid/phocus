<?php

namespace Szogyenyid\Phocus\Tests;

class NotImplementingMiddleware
{
    public function process(): bool
    {
        return true;
    }
}
