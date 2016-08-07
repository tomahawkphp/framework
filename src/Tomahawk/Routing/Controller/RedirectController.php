<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The is based on code originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Routing\Controller;

use Tomahawk\Config\ConfigInterface;
use Tomahawk\HttpCore\Request;
use Tomahawk\DependencyInjection\Container;
use Tomahawk\DependencyInjection\ContainerAwareInterface;
use Tomahawk\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RedirectController
 *
 * Based (heavily) on the RedirectController from the Symfony FrameworkBundle
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @package Tomahawk\Routing\Controller
 */
class RedirectController extends Container implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Redirects to a URL.
     *
     * The response status code is 302 if the permanent parameter is false (default),
     * and 301 if the redirection is permanent.
     *
     * In case the path is empty, the status code will be 404 when permanent is false
     * and 410 otherwise.
     *
     * @param Request $request
     * @param $path
     * @param bool|false $permanent
     * @param null $scheme
     * @param null $httpPort
     * @param null $httpsPort
     * @return RedirectResponse
     * @throws HttpException
     */
    public function urlRedirectAction(Request $request, $path, $permanent = false, $scheme = null, $httpPort = null, $httpsPort = null)
    {
        /** @var ConfigInterface $config */
        $config = $this->container->get('config');

        if ('' == $path) {
            throw new HttpException($permanent ? 410 : 404);
        }

        $statusCode = $permanent ? 301 : 302;
        // redirect if the path is a full URL

        if (parse_url($path, PHP_URL_SCHEME)) {
            return new RedirectResponse($path, $statusCode);
        }

        if (null === $scheme) {
            $scheme = $request->getScheme();
        }

        $qs = $request->getQueryString();

        if ($qs) {

            if (strpos($path, '?') === false) {
                $qs = '?'.$qs;
            }
            else {
                $qs = '&'.$qs;
            }
        }
        $port = '';

        if ('http' === $scheme) {

            if (null === $httpPort) {

                if ('http' === $request->getScheme()) {
                    $httpPort = $request->getPort();
                }
                elseif ($config->has('request.http_port')) {
                    $httpPort = $config->get('request.http_port');
                }
            }

            if (null !== $httpPort && 80 != $httpPort) {
                $port = ":$httpPort";
            }
        }
        elseif ('https' === $scheme) {

            if (null === $httpsPort) {

                if ('https' === $request->getScheme()) {
                    $httpsPort = $request->getPort();
                }
                elseif ($config->has('request.https_port')) {
                    $httpsPort = $config->get('request.https_port');
                }
            }

            if (null !== $httpsPort && 443 != $httpsPort) {
                $port = ":$httpsPort";
            }
        }

        $url = $scheme.'://'.$request->getHost().$port.$request->getBaseUrl().$path.$qs;

        return new RedirectResponse($url, $statusCode);
    }
}
