<?php

namespace Tomahawk\Generator\Tests;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Generator\ControllerGenerator;

class ControllerGeneratorTest extends GeneratorTest
{
    public function testGenerateController()
    {
        $generator = $this->getGenerator();
        $generator->generate($this->tmpDir .'/App/Controller', 'App\Controller', 'WelcomeController');

        $files = array(
            'App/Controller/WelcomeController.php',
        );

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/App/Controller/WelcomeController.php');
        $strings = array(
            'namespace App\\Controller',
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
        $this->filesystem->mkdir($this->tmpDir.'/App/Controller');
        $this->filesystem->touch($this->tmpDir.'/App/Controller/PageController.php');

        $this->getGenerator()->generate($this->tmpDir .'/App/Controller', 'App\Controller', 'PageController');

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

        $generator->generate($this->tmpDir .'/App/Controller', 'App\Controller', 'PageController', $actions);

        $content = file_get_contents($this->tmpDir.'/App/Controller/PageController.php');
        $strings = array(
            'public function showPageAction($id, $slug)',
            'public function getListOfPagesAction($max_count)',
        );

        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    public function testGenerateControllerWithInvalidActions()
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

        $generator->generate($this->tmpDir .'/App/Controller', 'App\Controller', 'PageController', $actions);

        $content = file_get_contents($this->tmpDir.'/App/Controller/PageController.php');
        $strings = array(
            'public function showPageAction($id, $slug)',
            'public function getListOfPagesAction($max_count)',
        );

        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    /**
     * @return ControllerGenerator
     */
    protected function getGenerator()
    {
        $generator = new ControllerGenerator($this->filesystem);
        $generator->setSkeletonDirs([__DIR__.'/../Resources/skeleton']);

        return $generator;
    }
}
