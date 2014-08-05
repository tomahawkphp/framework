<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Bundle\GeneratorBundle\Generator\ModelGenerator;

class ModelGeneratorTest extends GeneratorTest
{
    public function testGenerateModel()
    {
        $generator = $this->getGenerator();

        $generator->generate($this->getBundle(), 'User');

        $files = array(
            'Model/User.php',
        );

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Model/User.php');
        $strings = array(
            'namespace Foo\\BarBundle\\Model',
            'class User',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateModelFileExists()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Model');
        $this->filesystem->touch($this->tmpDir.'/Model/Page.php');

        $this->getGenerator()->generate($this->getBundle(), 'Page');

    }

    /**
     * @return BundleInterface
     */
    protected function getBundle()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue('FooBarBundle'));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));

        return $bundle;
    }

    /**
     * @return ModelGenerator
     */
    protected function getGenerator()
    {
        $generator = new ModelGenerator($this->filesystem);
        $generator->setSkeletonDirs(__DIR__.'/../Resources/skeleton');

        return $generator;
    }

}