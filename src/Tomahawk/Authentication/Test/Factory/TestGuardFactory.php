<?php

namespace Tomahawk\Authentication\Test\Factory;

use Tomahawk\Authentication\Factory\GuardFactoryInterface;
use Tomahawk\Authentication\Guard\GuardInterface;
use Tomahawk\Authentication\Test\Guard\TestGuard;

/**
 * Class TestGuardFactory
 * @package Tomahawk\Authentication\Test\Factory
 */
class TestGuardFactory implements GuardFactoryInterface
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
     * @return GuardInterface
     */
    public function make(string $name, array $config = [])
    {
        return new TestGuard($name);
    }
}
