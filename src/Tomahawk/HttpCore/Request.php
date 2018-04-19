<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\HttpCore;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tomahawk\Session\SessionInterface;

/**
 * Class Request
 * @package Tomahawk\HttpCore
 */
class Request extends SymfonyRequest
{
    public function getOld($name, $default = null)
    {
        /** @var SessionInterface $session */
        $session = $this->getSession();
        return $session->getOldInputBag()->get($name, $default);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface|SessionInterface
     */
    public function session()
    {
        return $this->session;
    }
}
