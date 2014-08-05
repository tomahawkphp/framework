<?php

use Tomahawk\Templating\Twig\TwigEngine;
use Symfony\Component\Templating\TemplateReference;

class TwigEngineTest extends PHPUnit_Framework_TestCase
{
    public function testExistsWithTemplateInstances()
    {
        $engine = $this->getTwig();

        $this->assertTrue($engine->exists($this->getMockForAbstractClass('Twig_Template', array(), '', false)));
    }

    public function testExistsWithNonExistentTemplates()
    {
        $engine = $this->getTwig();

        $this->assertFalse($engine->exists('foobar'));
        $this->assertFalse($engine->exists(new TemplateReference('foorbar')));
    }

    public function testExistsWithTemplateWithSyntaxErrors()
    {
        $engine = $this->getTwig();

        $this->assertTrue($engine->exists('error'));
        $this->assertTrue($engine->exists(new TemplateReference('error')));
    }

    public function testExists()
    {
        $engine = $this->getTwig();

        $this->assertTrue($engine->exists('index'));
        $this->assertTrue($engine->exists(new TemplateReference('index')));

    }

    public function testRender()
    {
        $engine = $this->getTwig();

        $this->assertSame('foo', $engine->render('index'));
        $this->assertSame('foo', $engine->render(new TemplateReference('index')));

        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            'index' => 'foo',
            'foo.twig' => 'foo',
            'error' => '{{ foo }',
        )));

        $engine->render(new TwigTemplateStub($twig));
    }

    /**
     * @expectedException \Twig_Error_Syntax
     */
    public function testRenderWithError()
    {
        $engine = $this->getTwig();

        $engine->render(new TemplateReference('error'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoad()
    {
        $twig = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twig->expects($this->once())
            ->method('loadTemplate')
            ->will($this->throwException(new \Twig_Error_Loader('error')));

        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new TwigEngine($twig, $parser);
        $engine->render(new TemplateReference('index'));
    }

    public function testExistsTemplate()
    {
        $engine = $this->getTwig();

        $this->assertTrue($engine->exists(new TemplateReference('index')));
    }

    public function testExistsInvalidTemplate()
    {
        $engine = $this->getTwig();

        $this->assertFalse($engine->exists(new TemplateReference('bla')));
    }

    public function testSupportWithTwigExistsLoaderInterface()
    {
        $loader = $this->getMock('Twig_Loader_Filesystem');

        $loader->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));

        $twig = new \Twig_Environment($loader);
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new TwigEngine($twig, $parser);
        $this->assertTrue($engine->exists(new TemplateReference('index')));
    }

    public function testSupportWithTwigExistsLoaderInterfaceThrowsExceptionAndReturnsFalse()
    {
        $loader = $this->getMock('Twig_LoaderInterface');

        $loader->expects($this->once())
            ->method('getSource')
            ->will($this->throwException(new \Twig_Error_Loader('error')));

        $twig = new \Twig_Environment($loader);
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new TwigEngine($twig, $parser);
        $this->assertFalse($engine->exists(new TemplateReference('index')));
    }

    public function testSupportWithTwigExistsLoaderReturnsTrue()
    {
        $loader = $this->getMock('Twig_LoaderInterface');

        $loader->expects($this->once())
            ->method('getSource');

        $twig = new \Twig_Environment($loader);
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $engine = new TwigEngine($twig, $parser);
        $this->assertTrue($engine->exists(new TemplateReference('index')));
    }

    public function testSupports()
    {
        $engine = $this->getTwig();

        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            'index' => 'foo',
            'error' => '{{ foo }',
        )));

        $ref = new TemplateReference('index', 'twig');

        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface', array('parse'));
        $parser->expects($this->any())
                ->method('parse')
                ->will($this->returnValue($ref));

        $engine2 = new TwigEngine($twig, $parser);
        $this->assertTrue($engine2->supports($ref));

        $template = new TwigTemplateStub($twig);
        $this->assertTrue($engine->supports($template));
    }

    protected function getTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            'index' => 'foo',
            'foo.twig' => 'foo',
            'error' => '{{ foo }',
        )));
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        return new TwigEngine($twig, $parser);
    }

}

class TwigTemplateStub extends Twig_Template
{
    public function getTemplateName()
    {}

    public function doDisplay(array $context, array $blocks = array())
    {}
}
