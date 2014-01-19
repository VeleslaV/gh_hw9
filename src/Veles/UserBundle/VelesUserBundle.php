<?php

namespace Veles\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VelesUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
