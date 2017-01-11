<?php

use Tomahawk\Templating\TemplateReference;
use Tomahawk\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateReference as BaseTemplateReference;

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
     * @dataProvider parseProvider
     */
    public function testParse($name, $logicalName, $path, $ref)
    {
        $template = $this->parser->parse($name);
        $this->assertSame($ref->getLogicalName(), $template->getLogicalName());
        $this->assertSame($logicalName, $template->getLogicalName());
        $this->assertSame($path, $template->getPath());
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

    public function parseProvider()
    {
        return array(
            array('FooBundle:Post:index.php', 'FooBundle:Post:index.php', '@FooBundle/Resources/views/Post/index.php', new TemplateReference('FooBundle', 'Post', 'index', 'php')),
            array('FooBundle:Post:index.twig', 'FooBundle:Post:index.twig', '@FooBundle/Resources/views/Post/index.twig', new TemplateReference('FooBundle', 'Post', 'index', 'twig')),
            array('FooBundle:Post:index.php', 'FooBundle:Post:index.php', '@FooBundle/Resources/views/Post/index.php', new TemplateReference('FooBundle', 'Post', 'index', 'php')),
            array('SensioFooBundle:Post:index.php', 'SensioFooBundle:Post:index.php', '@SensioFooBundle/Resources/views/Post/index.php', new TemplateReference('SensioFooBundle', 'Post', 'index', 'php')),
            array('SensioCmsFooBundle:Post:index.php', 'SensioCmsFooBundle:Post:index.php', '@SensioCmsFooBundle/Resources/views/Post/index.php', new TemplateReference('SensioCmsFooBundle', 'Post', 'index', 'php')),
            array(':Post:index.php', ':Post:index.php', 'views/Post/index.php', new TemplateReference('', 'Post', 'index', 'php')),
            array('::index.php', '::index.php', 'views/index.php', new TemplateReference('', '', 'index', 'php')),
            array('index.php', 'index.php', 'index.php', new BaseTemplateReference('index.php', 'php')),
            array('FooBundle:Post:foo.bar.index.php', 'FooBundle:Post:foo.bar.index.php', '@FooBundle/Resources/views/Post/foo.bar.index.php', new TemplateReference('FooBundle', 'Post', 'foo.bar.index', 'php')),
            array('@FooBundle/Resources/views/layout.twig', '@FooBundle/Resources/views/layout.twig', '@FooBundle/Resources/views/layout.twig', new BaseTemplateReference('@FooBundle/Resources/views/layout.twig', 'twig')),
            array('@FooBundle/Foo/layout.twig', '@FooBundle/Foo/layout.twig', '@FooBundle/Foo/layout.twig', new BaseTemplateReference('@FooBundle/Foo/layout.twig', 'twig')),
            array('name.twig', 'name.twig', 'name.twig', new BaseTemplateReference('name.twig', 'twig')),
            array('name', 'name.php', 'name.php', new BaseTemplateReference('name.php', 'php')),
            array('default/index.php', 'default/index.php', 'default/index.php', new BaseTemplateReference('default/index.php', 'php')),
        );
    }
}
