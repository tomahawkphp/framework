<?php

namespace Tomahawk\Asset\Test;

use Tomahawk\Test\TestCase;
use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Asset\AssetManager;
use Tomahawk\Asset\AssetContainer;

class AssetsTest extends TestCase
{
    public function testName()
    {
        $head = new AssetContainer('head');
        $this->assertEquals('head', $head->getName());
        $head->setName('header');
        $this->assertEquals('header', $head->getName());
    }

    public function testLongSyntax()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);

        $head = new AssetContainer('head');

        $footer = new AssetContainer('footer');

        $head->addCss('style', 'style.css');
        $footer->addJs('jquery', 'jquery.js');

        $manager->addContainer($head);
        $manager->addContainer($footer);

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="style.css" media="all">', $manager->outputCss('head'));
        $this->assertCount(2, $manager->getContainers());
    }

    public function testShortSyntax()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);

        $manager->container('head')->addCss('style', 'style.css');
        $manager->container('footer')->addJs('jquery', 'jquery.js');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="style.css" media="all">', $manager->outputCss('head'));
        $this->assertCount(2, $manager->getContainers());
    }

    public function testEvenShorterSyntax()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $manager->addCss('style', 'style.css');

        $this->assertEquals('<link rel="stylesheet" type="text/css" href="style.css" media="all">', $manager->outputCss());
    }

    public function testValidDependencies()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $manager->addCss('style2', 'style2.css', array('style'));
        $manager->addCss('style', 'style.css');

        $manager->addJs('jquery', 'jquery.js');


        $result = '<link rel="stylesheet" type="text/css" href="style.css" media="all">' . PHP_EOL;
        $result .= '<link rel="stylesheet" type="text/css" href="style2.css" media="all">';

        $js_result = '<script src="jquery.js"></script>';

        $this->assertEquals($result, $manager->outputCss());

        $this->assertEquals($js_result, $manager->outputJs());
    }

    public function testOutputNoContainerOrAssets()
    {
        $html = new HtmlBuilder();
        $manager = new AssetManager($html);

        $this->assertEquals('', $manager->outputCss('footer'));
    }

    public function testOutputContainerNoAssets()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $head = new AssetContainer('head');
        $manager->addContainer($head);
        $this->assertEquals('', $manager->outputCss('head'));
    }

    public function testNonExistantDependency()
    {
        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $manager->addCss('style2', 'style2.css', array('style'));

        $result = '<link rel="stylesheet" type="text/css" href="style2.css" media="all">';

        $this->assertEquals($result, $manager->outputCss());
    }

    public function testSelfDependency()
    {
        $this->setExpectedException('\Exception');

        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $manager->addCss('style2', 'style2.css', array('style2'));
        $manager->outputCss();
    }

    public function testCircularDependency()
    {
        $this->setExpectedException('\Tomahawk\Asset\Exception\CircularDependencyException');

        $html = new HtmlBuilder();

        $manager = new AssetManager($html);
        $manager->addCss('style2', 'style2.css', array('style1'));
        $manager->addCss('style1', 'style1.css', array('style2'));
        $manager->outputCss();
    }
}
