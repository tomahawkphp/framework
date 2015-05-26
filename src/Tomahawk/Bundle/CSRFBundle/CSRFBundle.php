<?php

namespace Tomahawk\Bundle\CSRFBundle;

use Tomahawk\Bundle\CSRFBundle\DI\CSRFProvider;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;

class CSRFBundle extends Bundle implements ContainerAwareInterface
{
    public function boot()
    {
        $this->container->register(new CSRFProvider());
    }
}
