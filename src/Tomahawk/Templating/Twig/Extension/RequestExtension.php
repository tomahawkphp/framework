<?php

namespace Tomahawk\Templating\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestExtension extends \Twig_Extension
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
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('request', array($this, 'getRequest')),
            new \Twig_SimpleFunction('request_locale', array($this, 'getLocale')),
            new \Twig_SimpleFunction('request_parameter', array($this, 'getParameter')),
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
        return 'request';
    }
}
