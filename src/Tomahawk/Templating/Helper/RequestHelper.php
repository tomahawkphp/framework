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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\Helper\Helper;

/**
 * RequestHelper gives access to current request in the stack
 *
 * @author Tom Elis <tellishtc@gmail.com>
 *
 * @api
 */
class RequestHelper extends Helper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * Construct
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Get current request
     *
     * @throws \LogicException
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (!$request = $this->requestStack->getCurrentRequest()) {
            throw new \LogicException('There is no Request in the RequestStack');
        }

        return $request;
    }

    /**
     * Get locale
     *
     * @return null|string
     */
    public function getLocale()
    {
        return $this->getRequest()->getLocale();
    }

    /**
     * Get a parameter from the current request
     *
     * @param $key
     * @param $default
     * @param bool $deep
     * @return mixed
     */
    public function getParameter($key, $default = null, $deep = false)
    {
        return $this->getRequest()->get($key, $default, $deep);
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
        return 'request';
    }
}
