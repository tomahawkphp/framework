<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Twig;

use Tomahawk\Templating\TemplateReference;
use Tomahawk\Templating\Twig\Bridge\TwigEngine as BaseEngine;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * This engine knows how to render Twig templates.
 *
 * @author Tom Ellis
 *
 * Heavily based on the original by
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigEngine extends BaseEngine
{
    /**
     * @var FileLocatorInterface
     */
    protected $locator;

    /**
     * Constructor.
     *
     * @param \Twig_Environment           $environment A \Twig_Environment instance
     * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
     * @param FileLocatorInterface        $locator     A FileLocatorInterface instance
     */
    public function __construct(\Twig_Environment $environment, TemplateNameParserInterface $parser, FileLocatorInterface $locator)
    {
        parent::__construct($environment, $parser);

        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        try {
            return parent::render($name, $parameters);
        } catch (\Twig_Error $e) {
            if ($name instanceof TemplateReference && !method_exists($e, 'setSourceContext')) {
                try {
                    // try to get the real name of the template where the error occurred
                    $name = $e->getTemplateName();
                    $path = (string) $this->locator->locate($this->parser->parse($name));
                    $e->setTemplateName($path);
                } catch (\Exception $e2) {
                }
            }

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Twig_Error if something went wrong like a thrown exception while rendering the template
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }
}
