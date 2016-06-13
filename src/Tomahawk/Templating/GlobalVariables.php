<?php

namespace Tomahawk\Templating;

use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Session\SessionInterface;

class GlobalVariables
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
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->container->get('auth')->getUser();
    }

    /**
     * Get current request
     *
     * @return Request|null
     */
    public function getRequest()
    {
        if ($this->container->has('request_stack')) {
            return $this->container->get('request_stack')->getCurrentRequest();
        }
    }

    /**
     * Get current session
     *
     * @return null|SessionInterface
     */
    public function getSession()
    {
        if ($request = $this->getRequest()) {
            return $request->getSession();
        }
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    /**
     * Return if app is in debug mode
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->container->get('kernel')->isDebug();
    }
}
