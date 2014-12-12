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

use Tomahawk\Url\UrlGeneratorInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * UrlHelper generates urls to routes
 *
 * @author Tom Elis <tellishtc@gmail.com>
 *
 * @api
 */
class UrlHelper extends Helper
{

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * Construct
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Pass all method calls off to the UrlGenerator
     *
     * @param $method
     * @param array $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments = array())
    {
        if (method_exists($this->urlGenerator, $method)) {
            return call_user_func_array(array($this->urlGenerator, $method), $arguments);
        }

        throw new \BadMethodCallException(sprintf('Method "%s" does not exist on the UrlGenerator', $method));
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
        return 'url';
    }
}

