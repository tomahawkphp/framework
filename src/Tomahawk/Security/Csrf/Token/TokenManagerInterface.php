<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Security\Csrf\Token;

/**
 * Interface TokenManagerInterface
 *
 * @package Tomahawk\Security\Csrf\Token
 */
interface TokenManagerInterface
{
    /**
     * Generate new CSRF Token
     *
     * @return bool|string
     */
    public function generateToken();

    /**
     * Check if token has been set
     *
     * @return bool
     */
    public function hasToken();

    /**
     * Get token
     *
     * @return mixed
     */
    public function getToken();

    /**
     * Set Token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token);

    /**
     * Get token name
     *
     * @return string
     */
    public function getTokenName();
}
