<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;

class Controller implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function forward($controller, array $path = [], array $query = [])
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
    public function renderView($view, array $parameters = [])
    {
        return $this->container->get('templating')->render($view, $parameters);
    }

    /**
     * Returns a response
     *
     * @param $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    public function render($view, array $parameters = [], Response $response = null)
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
