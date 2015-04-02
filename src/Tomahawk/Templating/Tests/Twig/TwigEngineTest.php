<?php

use Tomahawk\Templating\Twig\TwigEngine;
use Symfony\Component\Templating\TemplateReference;

class TwigEngineTest extends PHPUnit_Framework_TestCase
{
    public function testRenderResponse()
    {
        $twig = $this->getTwig();

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $twig->renderResponse('index'));
    }

    protected function getTwig()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            'index' => 'foo',
            'foo.twig' => 'foo',
            'error' => '{{ foo }',
        )));
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');

        return new TwigEngine($twig, $parser, $locator);
    }

}
