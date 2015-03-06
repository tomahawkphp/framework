<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Helper;

use Tomahawk\Session\SessionInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * SessionHelper allows easy to the Session
 *
 * @author Tom Elis <tellishtc@gmail.com>
 *
 * @api
 */
class SessionHelper extends Helper
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Construct
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Pass all method calls off to the Session
     *
     * @param $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments = array())
    {
        if (method_exists($this->session, $method)) {
            return call_user_func_array(array($this->session, $method), $arguments);
        }

        throw new \BadMethodCallException(sprintf('Method "%s" does not exist on the Session', $method));
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'session';
    }
}
