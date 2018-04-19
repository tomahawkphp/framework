<?php

namespace Tomahawk\Authentication\Factory;

use Tomahawk\Authentication\User\UserProviderInterface;

/**
 * Class UserProviderFactoryInterface
 * @package Tomahawk\Authentication\Factory
 */
interface UserProviderFactoryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @param array $config
     * @return UserProviderInterface
     */
    public function make(string $name, array $config = []);
}
