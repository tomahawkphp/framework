<?php

namespace Tomahawk\Url;

use Symfony\Component\HttpFoundation\Request;
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

    protected $validUrlStartChars = array(
        '#',
        '//',
        'mailto:',
        'tel:'
    );

    /**
     * Constructor.
     *
     * @param RouteCollection      $routes  A RouteCollection instance
     * @param RequestContext       $context The context
     * @param LoggerInterface|null $logger  A logger instance
     *
     * @api
     */
    public function __construct(RouteCollection $routes, RequestContext $context, LoggerInterface $logger = null)
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->logger = $logger;
    }

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
     * @param string $path
     * @param array $extra
     * @param bool $secure
     * @return mixed|string
     */
    public function to($path = '', array $extra = array(), $secure = false)
    {
        // Check if valid URL
        if ($this->validateURL($path)) {

            $extra = implode('/', array_map('rawurlencode', $extra));

            return rtrim($path, '/') . $extra;
        }

        $scheme = $this->context->getScheme();

        // When in a test environment you might not have SSL enabled, so you can turn this off easily
        if ($secure && $this->sslOn) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        $host = $this->context->getHost();

        $port = '';

        if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
            $port = ':'.$this->context->getHttpPort();
        } elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
            $port = ':'.$this->context->getHttpsPort();
        }

        $extra = implode('/', array_map('rawurlencode', $extra));

        $path = trim($path . '/' . $extra, '/');

        $url = rtrim(sprintf('%s://%s%s/%s', $scheme, $host, $port, $path, '/'));

        return $url;
    }

    /**
     * @param string $url
     * @param array $extra
     * @param bool $secure
     * @return string
     */
    public function secureTo($url = '', array $extra = array(), $secure = true)
    {
        return $this->to($url, $extra, $secure);
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
        return $this->generate($name, $data);
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