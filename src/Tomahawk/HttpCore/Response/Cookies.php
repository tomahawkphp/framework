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

    protected $applicationKey;

    protected $path = null;

    protected $domain = null;

    public function __construct(Request $request, $applicationKey, $path = null, $domain = null)
    {
        $this->request = $request;
        $this->path = $path;
        $this->domain = $domain;
        $this->applicationKey = $applicationKey;
    }

    public function has($name)
    {
        return $this->request->cookies->has($name);
    }

    public function set($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        list($path, $domain) = $this->getPathAndDomain($path, $domain);

        $expire = ($minutes == 0) ? 0 : time() + ($minutes * 60);

        $hashedValue = $this->hash($value).'+'.$value;

        $cookie = new Cookie($name, $hashedValue, $expire, $path, $domain, $secure, $httpOnly);

        $this->cookies[$name] = $cookie;

        return $this;
    }

    public function get($name, $default = null)
    {
        if (($value = $this->request->cookies->get($name)) !== null)
        {
            return $this->parse($value) ?: $default;
        }

        return $default;
    }

    public function expire($name)
    {
        $this->request->cookies->remove($name);
    }

    public function getQueued()
    {
        return $this->cookies;
    }

    /**
     * Hash the cookie value.
     *
     * @param $value
     * @return string
     */
    protected function hash($value)
    {
        return hash_hmac('sha1', $value, $this->applicationKey);
    }

    /**
     * Parse a cookie value and check for tampering.
     *
     * @param $value
     * @return null|string
     */
    protected function parse($value)
    {
        $segments = explode('+', $value);

        // First we will make sure the cookie actually has enough segments to even
        // be valid as being set by the application. If it does not we will go
        // ahead and throw exceptions now since there the cookie is invalid.
        if ( ! (count($segments) >= 2))
        {
            return null;
        }

        $value = implode('+', array_slice($segments, 1));

        // Now we will check if the SHA-1 hash present in the first segment matches
        // the ShA-1 hash of the rest of the cookie value, since the hash should
        // have been set when the cookie was first created by the application.
        return $segments[0] === $this->hash($value) ? $value : null;
    }

    /**
     * @param string $path
     * @param string $domain
     * @return array
     */
    protected function getPathAndDomain($path, $domain)
    {
        return array($path ?: $this->path, $domain ?: $this->domain);
    }
}
