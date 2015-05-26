<?php

namespace Tomahawk\Bundle\CSRFBundle\Event;

use Tomahawk\Bundle\CSRFBundle\Exception\InvalidTokenException;
use Tomahawk\Bundle\CSRFBundle\Exception\TokenNotFoundException;
use Tomahawk\Bundle\CSRFBundle\Token\TokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TokenSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }
}
