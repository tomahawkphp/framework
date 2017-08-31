<?php

namespace Tomahawk\Templating\Tests\Twig\Loader;

use PHPUnit\Framework\TestCase;
use Tomahawk\Templating\TemplateReference;
use Tomahawk\Templating\Twig\Loader\FilesystemLoader;

class FilesystemLoaderTest extends TestCase
{
    public function testGetSource()
    {
        $parser = $this->createMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->createMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(__DIR__.'/../../Resources/views/layout.twig'))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        $loader->addPath(__DIR__.'/../../Resources/views', 'namespace');
        // Twig-style
        $this->assertEquals("This is a layout\n", $loader->getSourceContext('@namespace/layout.twig')->getCode());
        // Test its returned from cache on 2nd call
        $this->assertEquals("This is a layout\n", $loader->getSourceContext('@namespace/layout.twig')->getCode());
        // Symfony-style
        $this->assertEquals("This is a layout\n", $loader->getSourceContext('TwigBundle::layout.twig')->getCode());
    }
    public function testExists()
    {
        // should return true for templates that Twig does not find, but Symfony does
        $parser = $this->createMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $locator = $this->createMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue($template = __DIR__.'/../../Resources/views/layout.twig'))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        return $this->assertTrue($loader->exists($template));
    }
    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testTwigErrorIfLocatorThrowsInvalid()
    {
        $parser = $this->createMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'engine')))
        ;
        $locator = $this->createMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->throwException(new \InvalidArgumentException('Unable to find template "NonExistent".')))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        $loader->getCacheKey('name.engine');
    }
    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testTwigErrorIfLocatorReturnsFalse()
    {
        $parser = $this->createMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('name.engine')
            ->will($this->returnValue(new TemplateReference('', '', 'name', 'engine')))
        ;
        $locator = $this->createMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(false))
        ;
        $loader = new FilesystemLoader($locator, $parser);
        $loader->getCacheKey('name.engine');
    }
}
