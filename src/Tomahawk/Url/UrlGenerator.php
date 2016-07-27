<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Url;

use Symfony\Component\Routing\Generator\UrlGenerator as SymfonyUrlGenerator;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Psr\Log\LoggerInterface;

class UrlGenerator extends SymfonyUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var bool
     */
    protected $sslOn = true;

    /**
     * @var array
     */
    protected $validUrlStartChars = array(
        '#',
        '//',
        'mailto:',
        'tel:'
    );

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->context->getBaseUrl();
    }

    /**
     * @return mixed|string
     */
    public function getCurrentUrl()
    {
        return $this->to($this->context->getPathInfo(), array(), 'https' === $this->context->getScheme());
    }

    /**
     * URL to an asset
     *
     * @param $path
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function asset($path, array $extra = array(), $secure = false)
    {
        return $this->to($path, $extra, $secure, true);
    }

    /**
     * @param string $path
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return mixed|string
     */
    public function to($path = '', array $extra = array(), $secure = false, $asset = false)
    {
        // Check if valid URL
        if ($this->validateURL($path)) {
            $extra = implode('/', array_map('rawurlencode', $extra));
            return rtrim($path, '/') . $extra;
        }

        $scheme = $this->context->getScheme();
        $host = $this->context->getHost();
        $base = '';

        if ( ! $asset) {
            $base = $this->context->getBaseUrl();
        }
        $port = '';

        // When in a test environment you might not have SSL enabled, so you can turn this off easily
        if ($secure && $this->sslOn) {
            $scheme = 'https';
        }
        else {
            $scheme = 'http';
        }

        if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
            $port = ':'.$this->context->getHttpPort();
        }
        else if ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
            $port = ':'.$this->context->getHttpsPort();
        }

        $extra = implode('/', array_map('rawurlencode', $extra));

        $path = trim($path . '/' . $extra, '/');

        $url = rtrim(sprintf('%s://%s%s%s/%s', $scheme, $host, $port, $base, $path), '/');

        return $url;
    }

    /**
     * @param string $url
     * @param array $extra
     * @param bool $secure
     * @param bool $asset
     * @return string
     */
    public function secureTo($url = '', array $extra = array(), $secure = true, $asset = false)
    {
        return $this->to($url, $extra, $secure, $asset);
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param $url
     * @return bool
     */
    public function validateUrl($url)
    {
        foreach ($this->validUrlStartChars as $char) {
            if (0 === strpos($url, $char)) {
                return true;
            }
        }

        return false !== filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param $name
     * @param array $data
     * @return mixed|string
     */
    public function route($name, $data = array())
    {
        return $this->generate($name, $data, self::ABSOLUTE_URL);
    }

    /**
     * @param boolean $sslOn
     */
    public function setSslOn($sslOn)
    {
        $this->sslOn = $sslOn;
    }

    /**
     * @return boolean
     */
    public function getSslOn()
    {
        return $this->sslOn;
    }
}
