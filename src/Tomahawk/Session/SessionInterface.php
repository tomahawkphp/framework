<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Session;

use Tomahawk\Session\Bag\OldInputBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface as SymfonySessionInterface;

/**
 * Interface SessionInterface
 * @package Tomahawk\Session
 */
interface SessionInterface extends SymfonySessionInterface
{
    /**
     * Get Old Input
     *
     * @return OldInputBagInterface
     */
    public function getOldInputBag();

    /**
     * Gets the flashbag interface.
     *
     * @return FlashBagInterface
     */
    public function getFlashBag();
}
