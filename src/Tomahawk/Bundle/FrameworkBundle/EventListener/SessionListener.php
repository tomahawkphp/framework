<?php

namespace Tomahawk\Bundle\FrameworkBundle\EventListener;

use Tomahawk\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\EventListener\SessionListener as SymfonySessionListener;

class SessionListener extends SymfonySessionListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets the session object.
     *
     * @return SessionInterface|null A SessionInterface instance or null if no session is available
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }
}
