<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing;

use Tomahawk\Auth\AuthInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tomahawk\Database\DatabaseManager;
use Tomahawk\Hashing\HasherInterface;
use Tomahawk\Forms\FormsManagerInterface;
use Tomahawk\Asset\AssetManagerInterface;
use Tomahawk\Encryption\CryptInterface;
use Tomahawk\DI\ContainerInterface;
use Tomahawk\Session\SessionInterface;
use Tomahawk\HttpCore\Response\CookiesInterface;
use Tomahawk\Cache\CacheInterface;
use Tomahawk\HttpCore\ResponseBuilderInterface;
use Symfony\Component\Templating\EngineInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Url\UrlGeneratorInterface;

class Controller
{
    /**
     * @var \Tomahawk\Auth\AuthInterface
     */
    protected $auth;

    /**
     * @var \Tomahawk\Forms\FormsManagerInterface
     */
    protected $forms;

    /**
     * @var \Tomahawk\Hashing\HasherInterface
     */
    protected $hasher;

    /**
     * @var \Tomahawk\Encryption\CryptInterface
     */
    protected $crypt;

    /**
     * @var \Tomahawk\Asset\AssetManagerInterface
     */
    protected $assets;

    /**
     * @var \Tomahawk\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Tomahawk\Database\DatabaseManager
     */
    protected $database;

    /**
     * @var \Tomahawk\HttpCore\Response\CookiesInterface
     */
    protected $cookies;

    /**
     * @var \Tomahawk\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @var \Tomahawk\HttpCore\ResponseBuilderInterface
     */
    protected $response;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Tomahawk\Config\ConfigInterface
     */
    protected $config;

    /**
     * @var \Tomahawk\DI\ContainerInterface
     */
    protected $container;

    /**
     * @var
     */
    protected $url;

    public function __construct(
        AuthInterface $auth,
        FormsManagerInterface $forms,
        CookiesInterface $cookies,
        AssetManagerInterface $assets,
        HasherInterface $hasher,
        SessionInterface $session = null,
        CryptInterface $crypt,
        CacheInterface $cache,
        ResponseBuilderInterface $response,
        EngineInterface $templating,
        ConfigInterface $config,
        ContainerInterface $container,
        DatabaseManager $database = null,
        UrlGeneratorInterface $url
    )
    {
        $this->auth = $auth;
        $this->forms = $forms;
        $this->cookies = $cookies;
        $this->assets = $assets;
        $this->hasher = $hasher;
        $this->session = $session;
        $this->database = $database;
        $this->crypt = $crypt;
        $this->cache = $cache;
        $this->response = $response;
        $this->templating = $templating;
        $this->config = $config;
        $this->container = $container;
        $this->url = $url;
    }


    public function forward($controller, array $path = array(), array $query = array())
    {
        $path['_controller'] = $controller;
        $subRequest = $this->container->get('http_kernel')->getCurrentRequest()->duplicate($query, null, $path);

        return $this->container->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $content = $this->renderView($view, $parameters);

        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool    true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}
