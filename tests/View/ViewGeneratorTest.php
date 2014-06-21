<?php

use Tomahawk\View\ViewGenerator;
use Tomahawk\Templating\BlocksHelper;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

class ViewGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Tomahawk\View\ViewGenerator
     */
    protected $view;

    protected $directoryPathPatterns;

    public function setup()
    {
        $this->directoryPathPatterns = array(
            __DIR__.'/views/%name%.php'
        );

        $this->view = new ViewGenerator($this->directoryPathPatterns, array(
            new BlocksHelper(),
            new SlotsHelper()
        ));
    }

    public function testGetDirectories()
    {
        $this->assertCount(1, $this->view->getDirectoryPathPatterns());
    }

    public function testViewNoParams()
    {
        $this->view->render('home');
    }

    public function testViewWithParams()
    {
        $this->view->render('user', array(
            'name' => 'Tomgrohl'
        ));
    }

    public function testViewWithSharedVariables()
    {
        $this->view->share('name', 'Tomgrohl');

        $this->view->render('user');
    }

    public function testBlocksHelper()
    {
        $viewString = $this->view->render('block');

        $this->assertEquals('hello world', $viewString);
    }

    public function testAddDirectories()
    {
        $this->view->setDirectoryPathPatterns(array(
            __DIR__.'/views/%name%.mobile.php',
            __DIR__.'/views/%name%.php'
        ));

        $this->assertCount(2, $this->view->getDirectoryPathPatterns());
    }

    public function testCorrectViewIsRendered()
    {
        $this->view->setDirectoryPathPatterns(array(
            __DIR__.'/views/%name%.mobile.php',
            __DIR__.'/views/%name%.php'
        ));

        $this->assertCount(2, $this->view->getDirectoryPathPatterns());

        $viewString = $this->view->render('user', array(
            'name' => 'tom'
        ));

        $this->assertEquals('hello tom from mobile', $viewString);
    }

}