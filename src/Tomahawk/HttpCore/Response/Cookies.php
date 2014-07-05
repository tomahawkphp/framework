<?php

namespace Tomahawk\HttpCore\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

class Cookies implements CookiesInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Cookie array
     */
    protected $cookies = array();

    /**
     * @var array
     */
    protected $config;

    public function __construct(Request $request, array $config)
    {
        $this->request = $request;
        $this->config = $config;
    }

    public function has($name)
    {
        return $this->request->cookies->has($name);
    }

    public function set($name, $value)
    {
        $cookie = new Cookie($name, $value);

        $this->cookies[$name] = $cookie;

        return $this;
    }

    public function get($name, $default = null)
    {
        return $this->request->cookies->get($name, $default);
    }

    public function expire($name)
    {
        $this->request->cookies->remove($name);
    }

    public function getQueued()
    {
        return $this->cookies;
    }
}