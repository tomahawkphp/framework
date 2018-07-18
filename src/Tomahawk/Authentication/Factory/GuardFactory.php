<?php

namespace Tomahawk\Authentication\Factory;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\Guard\SessionGuard;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Hashing\HasherInterface;
use Tomahawk\Session\SessionInterface;

/**
 * Class GuardFactory
 * @package Tomahawk\Authentication\Factory
 */
class GuardFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var UserProviderFactory
     */
    protected $userProviderFactory;

    /**
     * @var array
     */
    protected $createdGuards = [];

    /**
     * @var array
     */
    protected static $customGuards = [];

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var array
     */
    protected $customCreators;

    public function __construct(
        ContainerInterface $container,
        UserProviderFactory $userProviderFactory,
        ConfigInterface $config,
        array $customCreators = []
    )
    {
        $this->container = $container;
        $this->userProviderFactory = $userProviderFactory;
        $this->config = $config;
        $this->customCreators = $customCreators;
    }

    /**
     * @param string $name
     * @return GuardInterface
     */
    public function make(string $name)
    {
        return $this->createGuard($name);
    }

    /**
     * @param string $name
     * @throws InvalidArgumentException
     * @return GuardInterface
     */
    protected function createGuard(string $name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Authentication guard [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException("Authentication guard [{$name}] is not defined.");
    }

    /**
     * @param string $name
     * @param array $config
     * @return GuardInterface
     */
    protected function callCustomCreator(string $name, array $config)
    {
        $guard = $this->customCreators[$config['driver']]->make($name, $config);

        return $this->createdGuards[$name] = $guard;
    }

    /**
     * @param string $name
     * @param array $config
     * @return GuardInterface
     */
    protected function createSessionDriver(string $name, array $config)
    {
        $userProviderDriver = $config['provider'];
        $userProvider = $this->userProviderFactory->make($userProviderDriver);

        $sessionGuard = new SessionGuard(
            $name,
            $this->container->get(SessionInterface::class),
            $userProvider,
            $this->container->get(HasherInterface::class)
        );

        return $this->createdGuards[$name] = $sessionGuard;
    }

    /**
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name)
    {
        return $this->config->get("auth.guards.{$name}");
    }
}
