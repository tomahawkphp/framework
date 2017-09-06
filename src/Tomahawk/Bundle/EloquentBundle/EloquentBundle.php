<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\EloquentBundle;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tomahawk\Bundle\EloquentBundle\EventListener\GlobalListener;
use Tomahawk\Bundle\EloquentBundle\DependencyInjection\DatabaseProvider;
use Tomahawk\Bundle\EloquentBundle\DependencyInjection\MigrationServiceProvider;
use Tomahawk\HttpKernel\Bundle\Bundle;

/**
 * Class EloquentBundle
 * @package Tomahawk\Bundle\EloquentBundle
 */
class EloquentBundle extends Bundle
{
    public function boot()
    {
        $this->container->register(new DatabaseProvider());
        $this->container->register(new MigrationServiceProvider());
    }

    public function registerEvents(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new GlobalListener($this->container));
    }
}
