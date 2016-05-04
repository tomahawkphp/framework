<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating;

use Tomahawk\Authentication\User\UserInterface;
use Tomahawk\DependencyInjection\ContainerInterface;

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
        //return $this->container->get('auth')->
    }
}
