<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\FrameworkBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Class LocaleListener
 * @package Tomahawk\Bundle\FrameworkBundle\Events
 */
class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;
    private $requestStack;
    private $requestContext;

    public function __construct($defaultLocale = 'en', RequestStack $requestStack, RequestContext $requestContext = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setDefaultLocale($this->defaultLocale);

        $this->setLocale($request);
        $this->setContext($request);
    }

    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if (null !== $parentRequest = $this->requestStack->getParentRequest()) {
            $this->setContext($parentRequest);
        }
    }

    private function setLocale(Request $request)
    {
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        }
    }

    private function setContext(Request $request)
    {
        if (null !== $this->requestContext) {
            $this->requestContext->setParameter('_locale', $request->getLocale());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the RequestContext to have access to the _locale
            KernelEvents::REQUEST => array(array('onKernelRequest', 16)),
            KernelEvents::FINISH_REQUEST => array(array('onKernelFinishRequest', 0)),
        );
    }
}
