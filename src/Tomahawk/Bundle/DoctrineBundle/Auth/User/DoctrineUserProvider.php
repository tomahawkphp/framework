<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\DoctrineBundle\Auth\User;

use Tomahawk\Auth\User\UserInterface;
use Tomahawk\Auth\User\UserProviderInterface;
use Tomahawk\Bundle\DoctrineBundle\Registry;

/**
 * Class DoctrineUserProvider
 *
 * @package Tomahawk\Bundle\DoctrineBundle\Auth\User
 */
class DoctrineUserProvider implements UserProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var string
     */
    private $username;

    public function __construct(Registry $registry, $userClass, $username)
    {
        $this->registry = $registry;
        $this->userClass = $userClass;
        $this->username = $username;
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return UserInterface|null
     */
    public function findUserByUsername($username)
    {
        return $this->registry->getRepository($this->userClass)
            ->findOneBy(array(
                $this->username => $username
            ));
    }
}
