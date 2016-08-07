<?php

namespace Tomahawk\Routing\Matcher;

use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseRedirectableUrlMatcher;

class RedirectableUrlMatcher extends BaseRedirectableUrlMatcher
{
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
}
