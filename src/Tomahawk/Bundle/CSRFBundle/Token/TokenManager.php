<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\CSRFBundle\Token;

use Tomahawk\Common\Str;
use Tomahawk\Session\SessionInterface;

class TokenManager implements TokenManagerInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $tokenName;

    /**
     * @param SessionInterface $session
     * @param string $tokenName
     */
    public function __construct(SessionInterface $session, $tokenName = '_csrf_token')
    {
        $this->session = $session;
        $this->tokenName = $tokenName;
    }

    /**
     * Generate new CSRF Token
     *
     * @return bool|string
     */
    public function generateToken()
    {
        /*if ($this->hasToken()) {
            return $this->getToken();
        }*/

        $token = Str::random();

        $this->setToken($token);

        return $token;
    }

    /**
     * Check if token has been set
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->session->has($this->getTokenName());
    }

    /**
     * Get token
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->session->get($this->getTokenName());
    }

    /**
     * Set Token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->session->set($this->getTokenName(), $token);
        return $this;
    }

    /**
     * Get token name
     *
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }
}
