<?php

namespace Szogyenyid\Phocus\Examples;

use Szogyenyid\Phocus\AbstractView;

class ParameterView extends AbstractView
{
    public function __construct(string $firstname, string $lastname, User $user)
    {
        parent::__construct(
            __DIR__ . '/../../templates/my-view.php',
            $firstname,
            $lastname,
            $user
        );
    }
}
