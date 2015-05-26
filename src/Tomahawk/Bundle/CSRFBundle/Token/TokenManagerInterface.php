<?php

namespace Tomahawk\Bundle\CSRFBundle\Token;

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
