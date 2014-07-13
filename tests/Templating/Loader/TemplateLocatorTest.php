<?php

use Tomahawk\Templating\Loader\TemplateLocator;
use Tomahawk\Templating\TemplateReference;

class TemplateLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testLocateATemplate()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'engine');

        $fileLocator = $this->getFileLocator();

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->with($template->getPath())
            ->will($this->returnValue('/path/to/template'))
        ;

        $locator = new TemplateLocator($fileLocator);

        $this->assertEquals('/path/to/template', $locator->locate($template));
    }

    public function testThrowsExceptionWhenTemplateNotFound()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'engine');

        $fileLocator = $this->getFileLocator();

        $errorMessage = 'FileLocator exception message';

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->will($this->throwException(new \InvalidArgumentException($errorMessage)))
        ;

        $locator = new TemplateLocator($fileLocator);

        try {
            $locator->locate($template);
            $this->fail('->locate() should throw an exception when the file is not found.');
        } catch (\InvalidArgumentException $e) {
            $this->assertContains(
                $errorMessage,
                $e->getMessage(),
                'TemplateLocator exception should propagate the FileLocator exception message'
            );
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsAnExceptionWhenTemplateIsNotATemplateReferenceInterface()
    {
        $locator = new TemplateLocator($this->getFileLocator());
        $locator->locate('template');
    }

    protected function getFileLocator()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Config\FileLocator')
            ->setMethods(array('locate'))
            ->setConstructorArgs(array('/path/to/fallback'))
            ->getMock()
            ;
    }
}