<?php

/*
 * This file is part of the TomahawkPHP package.
 *
 * (c) Tom Ellis
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tomahawk\Templating;

use Symfony\Component\Templating\TemplateNameParser as BaseTemplateNameParser;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Tomahawk\HttpKernel\KernelInterface;

/**
 * Class TemplateNameParser
 * @package Tomahawk\Templating
 *
 * @author Tom Ellis
 *
 * Based on the original by:
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TemplateNameParser extends BaseTemplateNameParser
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        // No extension? Default to php
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        // This is horrible and I don't know why I did it
        // Will deprecate in a future version
        if ( ! $ext) {
            $name .= '.php';
        }

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        // normalize name
        $name = str_replace(':/', ':', preg_replace('#/{2,}#', '/', strtr($name, '\\', '/')));

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('Template name "%s" contains invalid characters.', $name));
        }

        if ($this->isAbsolutePath($name) || !preg_match('/^([^:]*):([^:]*):(.+)\.([^\.]+)$/', $name, $matches) || 0 === strpos($name, '@')) {
            return parent::parse($name);
        }

        $template = new TemplateReference($matches[1], $matches[2], $matches[3], $matches[4]);

        if ($template->get('bundle')) {
            try {
                $this->kernel->getBundle($template->get('bundle'));
            }
            catch (\Exception $e) {
                throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid.', $name), 0, $e);
            }
        }

        return $this->cache[$name] = $template;
    }

    /**
     * @param $file
     * @return bool
     */
    private function isAbsolutePath($file)
    {
        $isAbsolute = (bool) preg_match('#^(?:/|[a-zA-Z]:)#', $file);
        if ($isAbsolute) {
            @trigger_error('Absolute template path support is deprecated since Symfony 3.1 and will be removed in 4.0.', E_USER_DEPRECATED);
        }
        return $isAbsolute;
    }
}
