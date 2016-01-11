<?php

namespace GenericBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GenericBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
