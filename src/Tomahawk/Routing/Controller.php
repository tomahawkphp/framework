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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tomahawk\Auth\AuthInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\Hashing\HasherInterface;
use Tomahawk\Forms\FormsManagerInterface;
use Tomahawk\Asset\AssetManagerInterface;
use Tomahawk\DependencyInjection\ContainerInterface;
use Tomahawk\Session\SessionInterface;
use Tomahawk\HttpCore\Response\CookiesInterface;
use Tomahawk\Cache\CacheInterface;
use Tomahawk\HttpCore\ResponseBuilderInterface;
use Tomahawk\Config\ConfigInterface;
use Tomahawk\Url\UrlGeneratorInterface;
use Tomahawk\Input\InputInterface;

class Controller implements ContainerAwareInterface
{
    /**
     * @var \Tomahawk\DependencyInjection\ContainerInterface
     */
    protected $container;

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

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
