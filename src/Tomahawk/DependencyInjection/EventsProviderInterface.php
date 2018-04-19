<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\DependencyInjection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface EventsProviderInterface
 * @package Tomahawk\DependencyInjection
 */
interface EventsProviderInterface
{
    /**
     * @param ContainerInterface $container An Container instance
     * @param EventDispatcherInterface $eventDispatcher
     * @return
     */
    public function subscribe(ContainerInterface $container, EventDispatcherInterface $eventDispatcher);
}
