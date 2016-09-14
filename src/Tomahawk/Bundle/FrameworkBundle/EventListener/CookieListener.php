<?php

namespace Tomahawk\Bundle\FrameworkBundle\EventListener;

use Tomahawk\HttpCore\Response\Cookies;
use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CookieListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        /** @var Cookies $cookies */
        $cookies = $this->container->get('cookies');

        foreach ($cookies->getQueued() as $cookie) {
            $event->getResponse()->headers->setCookie($cookie);
        }

        $cookies->clearQueued();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array(array('onKernelResponse', 0)),
        );
    }
}
