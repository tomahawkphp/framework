<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\Bundle\GeneratorBundle\Generator\BundleGenerator;

class BundleGeneratorTest extends GeneratorTest
{
    public function testGenerateBundle()
    {
        $generator = $this->getGenerator();

        $generator->generate('Acme', 'AcmeBundle', $this->tmpDir);

        $files = array(
            'Acme/AcmeBundle.php',
        );

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        /*$content = file_get_contents($this->tmpDir.'/Controller/WelcomeController.php');
        $strings = array(
            'namespace Foo\\BarBundle\\Controller',
            'class WelcomeController',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }*/
    }

    public function testDirIsFile()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo');
        $this->filesystem->touch($this->tmpDir.'/Foo/BarBundle');

        try {
            $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, 'yml', false);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->assertEquals(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($this->tmpDir.'/Foo/BarBundle')), $e->getMessage());
        }
    }

    /*public function testIsNotWritableDir()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo2/BarBundle');
        $this->filesystem->chmod($this->tmpDir.'/Foo2/BarBundle', 0444);

        //var_dump(substr(sprintf('%o', fileperms($this->tmpDir.'/Foo/BarBundle')), -4));

        try {
            $this->getGenerator()->generate('Foo2\BarBundle', 'FooBarBundle', $this->tmpDir, 'yml', false);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->filesystem->chmod($this->tmpDir.'/Foo2/BarBundle', 0777);
            $this->assertEquals(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($this->tmpDir.'/Foo2/BarBundle')), $e->getMessage());
        }
    }*/

    public function testIsNotEmptyDir()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo/BarBundle');
        $this->filesystem->touch($this->tmpDir.'/Foo/BarBundle/somefile');

        try {
            $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, 'yml', false);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->filesystem->chmod($this->tmpDir.'/Foo/BarBundle', 0777);
            $this->assertEquals(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($this->tmpDir.'/Foo/BarBundle')), $e->getMessage());
        }
    }

    public function testExistingEmptyDirIsFine()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo/BarBundle');

        $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, 'yml', false);
    }

    /**
     * @return BundleGenerator
     */
    protected function getGenerator()
    {
        $generator = new BundleGenerator($this->filesystem);
        $generator->setSkeletonDirs(__DIR__.'/../Resources/skeleton');

        return $generator;
    }
}

