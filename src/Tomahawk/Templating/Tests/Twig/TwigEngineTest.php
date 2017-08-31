<?php

use Tomahawk\Templating\Twig\TwigEngine;
use Tomahawk\Templating\TemplateReference;

class TwigEngineTest extends PHPUnit\Framework\TestCase
{
    public function testRenderResponse()
    {
        $twig = $this->getTwig();

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $twig->renderResponse('index'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidViewThrowsException()
    {
        $twig = $this->getTwig();

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $twig->renderResponse('doesnt.exist'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderTemplateReferenceThrowsException()
    {
        $twig = $this->getTwig();

        $template = new TemplateReference(null, 'Section', 'index', 'php');

        $this->assertEquals('foo', $twig->render($template));
    }

    public function testRenderTemplateReferenceReturnsResponse()
    {
        $twig = $this->getTwig();

        $template = new TemplateReference(null, null, 'index', 'php');

        $this->assertEquals('foo', $twig->render($template));
    }

    protected function getTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            'index' => 'foo',
            '::index.php' => 'foo',
            'foo.twig' => 'foo',
            'error' => '{{ foo }',
        )));
        $parser = $this->createMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $locator = $this->createMock('Symfony\Component\Config\FileLocatorInterface');

        return new TwigEngine($twig, $parser, $locator);
    }

}
