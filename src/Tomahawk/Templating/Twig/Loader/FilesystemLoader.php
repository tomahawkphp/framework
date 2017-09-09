<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * The code is based off the Twig Bundle by the Symfony2 Project
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating\Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * FilesystemLoader extends the default Twig filesystem loader
 * to work with the Symfony paths and template references.
 *
 * @author Tom Ellis
 *
 * Heavily based on the original by:
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FilesystemLoader extends BaseFilesystemLoader
{
    /**
     * @var FileLocatorInterface
     */
    protected $locator;

    /**
     * @var TemplateNameParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param FileLocatorInterface        $locator  A FileLocatorInterface instance
     * @param TemplateNameParserInterface $parser   A TemplateNameParserInterface instance
     * @param string|null                 $rootPath The root path common to all relative paths (null for getcwd())
     */
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser, $rootPath = null)
    {
        parent::__construct(array(), $rootPath);

        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     *
     * The name parameter might also be a TemplateReferenceInterface.
     */
    public function exists($name)
    {
        return parent::exists((string) $name);
    }

    /**
     * Returns the path to the template file.
     *
     * The file locator is used to locate the template when the naming convention
     * is the symfony one (i.e. the name can be parsed).
     * Otherwise the template is located using the locator from the twig library.
     *
     * @param string|TemplateReferenceInterface $template The template
     * @param bool                              $throw    When true, a \Twig_Error_Loader exception will be thrown if a template could not be found
     *
     * @return string The path to the template file
     *
     * @throws LoaderError if the template could not be found
     */
    protected function findTemplate($template, $throw = true)
    {
        $logicalName = (string) $template;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $file = null;
        try {
            $file = parent::findTemplate($logicalName);
        } catch (LoaderError $e) {
            $twigLoaderException = $e;

            // for BC
            try {
                $template = $this->parser->parse($template);
                $file = $this->locator->locate($template);
            } catch (\Exception $e) {
            }
        }

        if (false === $file || null === $file) {
            if ($throw) {
                throw $twigLoaderException;
            }

            //@codeCoverageIgnoreStart
            return false;
            //@codeCoverageIgnoreEnd
        }

        return $this->cache[$logicalName] = $file;
    }
}
