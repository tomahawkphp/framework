<?php

namespace Tomahawk\Authentication\Factory;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Tomahawk\Authentication\User\InMemoryUserProvider;
use Tomahawk\Authentication\User\UserProviderInterface;
use Tomahawk\Config\ConfigInterface;

/**
 * Class UserProviderFactory
 * @package Tomahawk\Authentication\Factory
 */
class UserProviderFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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
        ConfigInterface $config,
        array $customCreators = []
    )
    {
        $this->container = $container;
        $this->config = $config;
        $this->customCreators = $customCreators;
    }

    /**
     * @param string $name
     * @return UserProviderInterface
     */
    public function make(string $name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Authentication user provider [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'UserProvider';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException("Authentication user provider [{$name}] is not defined.");

    }

    /**
     * @param array $config
     * @return UserProviderInterface
     */
    protected function createMemoryUserProvider($name, array $config)
    {
        return $this->container->get(InMemoryUserProvider::class);
    }


    /**
     * @param string $name
     * @return array|null
     */
    protected function getConfig(string $name)
    {
        return $this->config->get("auth.providers.{$name}");
    }

    /**
     * @param string $name
     * @param array $config
     * @return UserProviderInterface
     */
    protected function callCustomCreator(string $name, array $config)
    {
        return $this->customCreators[$config['driver']]->make($name, $config);
    }
}
