<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class GeneratorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;
    protected $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/tomahawk';
        $this->filesystem = new Filesystem();
        $this->filesystem->remove($this->tmpDir);
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->tmpDir);
    }
}