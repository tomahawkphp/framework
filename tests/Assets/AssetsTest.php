<?php

use Tomahawk\Html\HtmlBuilder;
use Tomahawk\Assets\AssetManager;
use Tomahawk\Assets\AssetContainer;

class AssetsTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
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


        $result = '<link rel="stylesheet" type="text/css" href="style.css" media="all">' . PHP_EOL;
        $result .= '<link rel="stylesheet" type="text/css" href="style2.css" media="all">';

        $this->assertEquals($result, $manager->outputCss());
    }
}