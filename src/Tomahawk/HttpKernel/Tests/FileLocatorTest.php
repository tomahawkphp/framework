<?php

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\HttpKernel\Config\FileLocator;
use PHPUnit_Framework_TestCase as TestCase;

class FileLocatorTest extends TestCase
{
    public function testLocate()
    {
        $kernel = $this->getMock('Tomahawk\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->atLeastOnce())
            ->method('locateResource')
            ->with('@BundleName/some/path', null, true)
            ->will($this->returnValue('/bundle-name/some/path'));
        $locator = new FileLocator($kernel);
        $this->assertEquals('/bundle-name/some/path', $locator->locate('@BundleName/some/path'));

        $kernel
            ->expects($this->never())
            ->method('locateResource');
        $this->setExpectedException('LogicException');
        $locator->locate('/some/path');
    }

    public function testLocateWithGlobalResourcePath()
    {
        $kernel = $this->getMock('Tomahawk\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->atLeastOnce())
            ->method('locateResource')
            ->with('@BundleName/some/path', '/global/resource/path', false);

        $locator = new FileLocator($kernel, '/global/resource/path');
        $locator->locate('@BundleName/some/path', null, false);
    }
}
