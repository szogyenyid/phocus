<?php

namespace Szogyenyid\Phocus\Examples;

use Szogyenyid\Phocus\AbstractView;

class BasicView extends AbstractView
{
    public function __construct()
    {
        parent::__construct(__DIR__ . '/../../templates/my-view.php');
    }
}
