<?php

namespace Szogyenyid\Phocus\Tests;

class TestController
{
    public static function foo()
    {
        echo "foo";
    }

    public function duplicate(int $number)
    {
        echo $number * 2;
    }
}
