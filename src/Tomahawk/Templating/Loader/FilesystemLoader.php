<?php

namespace Tomahawk\Templating\Loader;

use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class FilesystemLoader implements LoaderInterface
{
    protected $locator;

    /**
     * Constructor.
     *
     * @param FileLocatorInterface $locator A FileLocatorInterface instance
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function load(TemplateReferenceInterface $template)
    {
        try {
            $file = $this->locator->locate($template);
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return new FileStorage($file);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        if (false === $storage = $this->load($template)) {
            return false;
        }

        if (!is_readable((string) $storage)) {
            return false;
        }

        return filemtime((string) $storage) < $time;
    }
}