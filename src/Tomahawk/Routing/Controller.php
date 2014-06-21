<?php

namespace Tomahawk\Routing;

use Tomahawk\Database\DatabaseManager;
use Symfony\Component\HttpFoundation\Request;
use Tomahawk\Forms\FormsManagerInterface;
use Tomahawk\Assets\AssetManagerInterface;
use Tomahawk\Encryption\CryptInterface;
use Tomahawk\DI\DIContainerInterface;
use Tomahawk\Session\SessionInterface;
use Tomahawk\Http\Response\CookiesInterface;
use Tomahawk\Cache\CacheInterface;
use Tomahawk\Http\ResponseBuilderInterface;
use Tomahawk\View\ViewGeneratorInterface;

class Controller
{
    /**
     * @var \Tomahawk\Forms\FormsManagerInterface
     */
    protected $forms;

    /**
     * @var \Tomahawk\DI\DIContainerInterface
     */
    protected $di;

    /**
     * @var \Tomahawk\Encryption\CryptInterface
     */
    protected $crypt;

    /**
     * @var \Tomahawk\Assets\AssetManagerInterface
     */
    protected $assets;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Tomahawk\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Tomahawk\Database\DatabaseManager
     */
    protected $database;

    /**
     * @var \Tomahawk\Http\Response\CookiesInterface
     */
    protected $cookies;

    /**
     * @var \Tomahawk\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @var \Tomahawk\Http\ResponseBuilderInterface
     */
    protected $response;

    public function __construct(
        FormsManagerInterface $forms,
        DIContainerInterface $di,
        CookiesInterface $cookies,
        AssetManagerInterface $assets,
        Request $request,
        SessionInterface $session,
        DatabaseManager $database,
        CryptInterface $crypt,
        CacheInterface $cache,
        ResponseBuilderInterface $response,
        ViewGeneratorInterface $view
    )
    {
        $this->di = $di;
        $this->forms = $forms;
        $this->cookies = $cookies;
        $this->assets = $assets;
        $this->request = $request;
        $this->session = $session;
        $this->database = $database;
        $this->crypt = $crypt;
        $this->cache = $cache;
        $this->response = $response;
        $this->view = $view;
    }
}