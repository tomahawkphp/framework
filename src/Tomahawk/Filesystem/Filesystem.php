<?php

namespace Tomahawk\Filesystem;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * Class Filesystem
 * @package Tomahawk\Filesystem
 */
class Filesystem extends SymfonyFilesystem
{
    /**
     * Find path names matching a given pattern.
     *
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public function glob(string $pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * @param string $file
     */
    public function requireOnce(string $file)
    {
        require_once($file);
    }
}
