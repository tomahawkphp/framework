<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Security\Csrf\EventListener;

use Tomahawk\Security\Csrf\Exception\InvalidTokenException;
use Tomahawk\Security\Csrf\Exception\TokenNotFoundException;
use Tomahawk\Security\Csrf\Token\TokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class TokenListener
 *
 * @package Tomahawk\Security\Csrf\EventListener
 */
class TokenListener implements EventSubscriberInterface
{
    /**
     * @var TokenManagerInterface
     */
    protected $tokenManager;

    public function __construct(TokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * KernelEvents::REQUEST event listener
     *
     * @param GetResponseEvent $event
     * @throws InvalidTokenException
     * @throws TokenNotFoundException
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Does this request require CSRF protection
        if (true !== $request->get('filter_csrf')) {
            return;
        }

        $postedToken = $request->get($this->tokenManager->getTokenName());
        $actualToken = $this->tokenManager->getToken();

        // Check if token is set
        if ('POST' === $request->getMethod() && !$postedToken) {
            throw new TokenNotFoundException();
        }
        // Check if token is valid
        else if ('POST' === $request->getMethod()
            && $postedToken !== $actualToken) {
            throw new InvalidTokenException();
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }
}
