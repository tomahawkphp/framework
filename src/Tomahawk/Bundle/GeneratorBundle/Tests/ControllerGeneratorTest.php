<?php

namespace Tomahawk\Bundle\GeneratorBundle\Tests;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Bundle\GeneratorBundle\Generator\ControllerGenerator;

class ControllerGeneratorTest extends GeneratorTest
{
    public function testGenerateController()
    {
        $this->getGenerator()->generate($this->getBundle(), 'Welcome');

        $files = array(
            'Controller/WelcomeController.php',
        );

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/Controller/WelcomeController.php');
        $strings = array(
            'namespace Foo\\BarBundle\\Controller',
            'class WelcomeController',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateControllerFileExists()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Controller');
        $this->filesystem->touch($this->tmpDir.'/Controller/PageController.php');

        $this->getGenerator()->generate($this->getBundle(), 'Page');

    }

    public function testGenerateControllerActions()
    {
        $generator = $this->getGenerator();
        $actions = array(
            0 => array(
                'name' => 'showPageAction',
                'placeholders' => array('id', 'slug'),
            ),
            1 => array(
                'name' => 'getListOfPagesAction',
                'placeholders' => array('max_count'),
            ),
        );

        $generator->generate($this->getBundle(), 'Page', $actions);

        $content = file_get_contents($this->tmpDir.'/Controller/PageController.php');
        $strings = array(
            'public function showPageAction($id, $slug)',
            'public function getListOfPagesAction($max_count)',
        );

        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
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
     * @return ControllerGenerator
     */
    protected function getGenerator()
    {
        $generator = new ControllerGenerator($this->filesystem);
        $generator->setSkeletonDirs(__DIR__.'/../Resources/skeleton');

        return $generator;
    }
}