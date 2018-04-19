<?php

namespace Tomahawk\Authentication\Factory;

use Tomahawk\Authentication\Guard\GuardInterface;

/**
 * Class GuardFactory
 * @package Tomahawk\Authentication\Factory
 */
interface GuardFactoryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @param array $config
     * @return GuardInterface
     */
    public function make(string $name, array $config = []);
}
