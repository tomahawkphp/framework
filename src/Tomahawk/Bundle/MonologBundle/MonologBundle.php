<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\MonologBundle;

use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\HttpKernel\Bundle\Bundle;
use Tomahawk\Bundle\MonologBundle\DependencyInjection\MonologProvider;

class MonologBundle extends Bundle implements ContainerAwareInterface
{
    public function boot()
    {
        $this->container->register(new MonologProvider());
    }

}
