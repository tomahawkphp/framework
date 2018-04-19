<?php

namespace Tomahawk\Authentication\Test\Factory;

use Tomahawk\Authentication\Factory\GuardFactoryInterface;
use Tomahawk\Authentication\Factory\UserProviderFactoryInterface;
use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\Test\Guard\TestGuard;
use Tomahawk\Authentication\Test\TestUserProvider;
use Tomahawk\Authentication\User\UserProviderInterface;

/**
 * Class TestUserProviderFactory
 * @package Tomahawk\Authentication\Test\Factory
 */
class TestUserProviderFactory implements UserProviderFactoryInterface
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'test';
    }

    /**
     * @param string $name
     * @param array $config
     * @return UserProviderInterface
     */
    public function make(string $name, array $config = [])
    {
        return new TestUserProvider($name);
    }
}
