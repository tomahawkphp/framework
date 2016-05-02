<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Twig\Extension;

use Tomahawk\Session\SessionInterface;

class SessionExtension extends \Twig_Extension
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
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('session_get', array($this, 'get')),
            new \Twig_SimpleFunction('session_has', array($this, 'has')),
            new \Twig_SimpleFunction('flash_get', array($this, 'getFlashBag')),
        );
    }

    /**
     * Get a value off the session
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * Check if a session value exists
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->session->has($name);
    }

    /**
     * Get flash bag
     *
     * @return array
     */
    public function getFlashBag()
    {
        return $this->session->getFlashBag();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'session';
    }
}
