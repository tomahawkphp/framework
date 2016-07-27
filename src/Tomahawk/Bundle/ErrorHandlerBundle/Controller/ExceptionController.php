<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Bundle\ErrorHandlerBundle\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tomahawk\HttpCore\Request;
use Tomahawk\Routing\Controller;

/**
 * Class ExceptionController
 *
 * Inspired by the TwigBundle and WebProfilerBundle error handling
 *
 * @package Tomahawk\Bundle\ErrorHandlerBundle\Controller
 */
class ExceptionController extends Controller
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var boolean
     */
    private $debug;

    public function __construct(\Twig_Environment $twig, $debug)
    {
        $this->twig = $twig;
        $this->debug = $debug;
    }

    /**
     * @param Request $request
     * @param FlattenException $exception
     * @param DebugLoggerInterface|null $logger
     * @return string|Response
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $code = $exception->getStatusCode();

        $exceptionHandler = new ExceptionHandler($this->debug, $this->twig->getCharset());

        if ($this->debug) {
            return new Response($exceptionHandler->getHtml($exception), $code);
        }

        $code = $exception->getStatusCode();

        $template = sprintf('ErrorHandlerBundle:Error:error%s.twig', $code);

        $exceptionTemplate = sprintf('ErrorHandlerBundle:Error:exception.twig', $code);

        if ($this->templateExists($template)) {
            return new Response($this->twig->render($template, array(
                $exception,
            )), $code);
        }

        if ($this->templateExists($exceptionTemplate)) {
            return new Response($this->twig->render($exceptionTemplate, array(
                $exception,
            )), $code);
        }

        return new Response($exceptionHandler->getHtml($exception), $code);
    }

    /**
     *
     * Check if a twig template exists
     *
     * to be removed when the minimum required version of Twig is >= 3.0
     *
     * This logic has been taken from the Symfony TwigBundle
     *
     * @param $template
     * @return bool
     */
    protected function templateExists($template)
    {
        $template = (string) $template;
        $loader = $this->twig->getLoader();

        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            return $loader->exists($template);
        }
        try {
            $loader->getSource($template);
            return true;
        } catch (\Twig_Error_Loader $e) {

        }

        return false;
    }
}
