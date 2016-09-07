<?php

namespace Tomahawk\Routing\Matcher;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface;
use Symfony\Component\Routing\Route;

/**
 * Class RedirectableUrlMatcher
 *
 * Tries to match routes with and without an ending slash
 *
 * Based (heavily) on the RedirectableUrlMatcher from the Symfony FrameworkBundle
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @package Tomahawk\Routing\Matcher
 */
class RedirectableUrlMatcher extends UrlMatcher implements RedirectableUrlMatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        try {
            $parameters = parent::match($pathinfo);
        } catch (ResourceNotFoundException $e) {
            if ('/' !== substr($pathinfo, -1) || !in_array($this->context->getMethod(), array('HEAD', 'GET'))) {
                throw $e;
            }

            // If the path ends with a slash see if a route exists without it
            try {
                parent::match(rtrim($pathinfo, '/'));

                return $this->redirect(rtrim($pathinfo, '/'), null);
            } catch (ResourceNotFoundException $e2) {
                throw $e;
            }
        }

        return $parameters;
    }

    /**
     * Redirects the user to another URL.
     *
     * @param string $path The path info to redirect to
     * @param string $route The route name that matched
     * @param string|null $scheme The URL scheme (null to keep the current one)
     *
     * @return array An array of parameters
     */
    public function redirect($path, $route, $scheme = null)
    {
        return [
            '_controller' => 'Tomahawk\\Routing\\Controller\\RedirectController::urlRedirectAction',
            'path' => $path,
            'permanent' => true,
            'scheme' => $scheme,
            'httpPort' => $this->context->getHttpPort(),
            'httpsPort' => $this->context->getHttpsPort(),
            '_route' => $route,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRouteRequirements($pathinfo, $name, Route $route)
    {
        //@codeCoverageIgnoreStart

        // Ignoring as this is already tested by Symfony
        // expression condition
        if ($route->getCondition() && !$this->getExpressionLanguage()->evaluate($route->getCondition(), array('context' => $this->context, 'request' => $this->request))) {
            return array(self::REQUIREMENT_MISMATCH, null);
        }
        //@codeCoverageIgnoreEnd

        // check HTTP scheme requirement
        $scheme = $this->context->getScheme();
        $schemes = $route->getSchemes();
        if ($schemes && !$route->hasScheme($scheme)) {
            return array(self::ROUTE_MATCH, $this->redirect($pathinfo, $name, current($schemes)));
        }

        return array(self::REQUIREMENT_MATCH, null);
    }
}
