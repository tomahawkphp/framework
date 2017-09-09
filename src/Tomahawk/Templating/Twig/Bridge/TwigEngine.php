<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Twig\Bridge;

use Twig\Environment;
use Twig\Template;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Loader\ExistsLoaderInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\StreamingEngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * This engine knows how to render Twig templates.
 *
 * @author Tom Ellis
 *
 * Heavily based on the original by
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigEngine implements EngineInterface, StreamingEngineInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var TemplateNameParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param Environment $environment A \Twig\Environment instance
     * @param TemplateNameParserInterface $parser A TemplateNameParserInterface instance
     */
    public function __construct(Environment $environment, TemplateNameParserInterface $parser)
    {
        $this->environment = $environment;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     *
     * It also supports \Twig_Template as name parameter.
     *
     * @throws Error if something went wrong like a thrown exception while rendering the template
     */
    public function render($name, array $parameters = array())
    {
        return $this->load($name)->render($parameters);
    }

    /**
     * {@inheritdoc}
     *
     * It also supports \Twig_Template as name parameter.
     *
     * @throws Error if something went wrong like a thrown exception while rendering the template
     */
    public function stream($name, array $parameters = array())
    {
        $this->load($name)->display($parameters);
    }

    /**
     * {@inheritdoc}
     *
     * It also supports \Twig\Template as name parameter.
     */
    public function exists($name)
    {
        if ($name instanceof Template) {
            return true;
        }

        $loader = $this->environment->getLoader();

        return $loader->exists((string) $name);
    }

    /**
     * {@inheritdoc}
     *
     * It also supports \Twig\Template as name parameter.
     */
    public function supports($name)
    {
        if ($name instanceof Template) {
            return true;
        }

        $template = $this->parser->parse($name);

        return 'twig' === $template->get('engine');
    }

    /**
     * Loads the given template.
     *
     * @param string|TemplateReferenceInterface|Template $name A template name or an instance of
     *                                                               TemplateReferenceInterface or \Twig_Template
     *
     * @return Template A \Twig\Template instance
     *
     * @throws \InvalidArgumentException if the template does not exist
     */
    protected function load($name)
    {
        if ($name instanceof Template) {
            return $name;
        }

        try {
            return $this->environment->loadTemplate((string) $name);
        }
        catch (LoaderError $e) {
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
