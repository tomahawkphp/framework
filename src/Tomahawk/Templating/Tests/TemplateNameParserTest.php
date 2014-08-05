<?php

use Tomahawk\Templating\TemplateReference;
use Tomahawk\Templating\TemplateNameParser;

class TemplateNameParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateNameParser
     */
    protected $parser;

    protected function setUp()
    {
        $kernel = $this->getMock('Tomahawk\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnCallback(function ($bundle) {
                if (in_array($bundle, array('SensioFooBundle', 'SensioCmsFooBundle', 'FooBundle'))) {
                    return true;
                }

                throw new \InvalidArgumentException();
            }))
        ;
        $this->parser = new TemplateNameParser($kernel);
    }

    protected function tearDown()
    {
        $this->parser = null;
    }

    /**
     * @dataProvider getLogicalNameToTemplateProvider
     */
    public function testParse($name, $ref)
    {
        $template = $this->parser->parse($name);

        $this->assertEquals($template->getLogicalName(), $ref->getLogicalName());
        $this->assertEquals($template->getLogicalName(), $name);

    }

    public function testParseCache()
    {
        $name = 'FooBundle:Post:index.php';
        $template = $this->parser->parse($name);
        $template2 = $this->parser->parse($name);

        $this->assertEquals($template->getLogicalName(), $template2->getLogicalName());
    }

    public function testParseReference()
    {
        $ref = new TemplateReference('FooBundle', 'Post', 'index', 'php');
        $template = $this->parser->parse($ref);

        $this->assertEquals($ref, $template);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalid()
    {
        $name = 'FooBundle:Post:..index.php';
        $this->parser->parse($name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBundleExistence()
    {
        $name = 'BarBundle:Post:index.php';
        $this->parser->parse($name);
    }

    public function getLogicalNameToTemplateProvider()
    {
        return array(
            array('FooBundle:Post:index.php', new TemplateReference('FooBundle', 'Post', 'index', 'php')),
            array('FooBundle:Post:index.twig', new TemplateReference('FooBundle', 'Post', 'index','twig')),
            array('FooBundle:Post:index.php', new TemplateReference('FooBundle', 'Post', 'index', 'php')),
            array('SensioFooBundle:Post:index.php', new TemplateReference('SensioFooBundle', 'Post', 'index','php')),
            array('SensioCmsFooBundle:Post:index.php', new TemplateReference('SensioCmsFooBundle', 'Post', 'index','php')),
            array(':Post:index.php', new TemplateReference('', 'Post', 'index', 'php')),
            array('::index.php', new TemplateReference('', '', 'index', 'php')),
            array('FooBundle:Post:foo.bar.index.php', new TemplateReference('FooBundle', 'Post', 'foo.bar.index','php')),
        );
    }

    /**
     * @dataProvider      getInvalidLogicalNameProvider
     * @expectedException \InvalidArgumentException
     */
    public function testParseInvalidName($name = null)
    {
        $this->parser->parse($name);
    }

    public function getInvalidLogicalNameProvider()
    {
        return array(
            array('FooBundle:Post'),
            array('FooBundle:Post:'),
        );
    }

}