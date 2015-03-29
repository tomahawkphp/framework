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

use Tomahawk\Input\InputInterface;

class InputExtension extends \Twig_Extension
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * Construct
     *
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('request', array($this, 'getRequest')),
            new \Twig_SimpleFunction('locale', array($this, 'getLocale')),
            new \Twig_SimpleFunction('parameter', array($this, 'getParameter')),
        );
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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'input';
    }
}
