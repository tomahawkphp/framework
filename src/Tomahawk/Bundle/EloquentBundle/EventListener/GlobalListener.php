<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\EloquentBundle\EventListener;

use Illuminate\Database\Capsule\Manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tomahawk\DependencyInjection\ContainerInterface;

/**
 * Class GlobalListener
 * @package Tomahawk\Bundle\EloquentBundle\EventListener
 */
class GlobalListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * KernelEvents::REQUEST event listener
     *
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $this->container->get(Manager::class);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => ['onRequest', -128],
        );
    }
}
