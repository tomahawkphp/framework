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

use Twig\Environment;
use Twig\Error\Error;
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
class TwigEngine extends BaseEngine implements EngineInterface
{
    /**
     * @var FileLocatorInterface
     */
    protected $locator;

    /**
     * Constructor.
     *
     * @param Environment           $environment A Environment instance
     * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
     * @param FileLocatorInterface        $locator     A FileLocatorInterface instance
     */
    public function __construct(Environment $environment, TemplateNameParserInterface $parser, FileLocatorInterface $locator)
    {
        parent::__construct($environment, $parser);

        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Error if something went wrong like a thrown exception while rendering the template
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
