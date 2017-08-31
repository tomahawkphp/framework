<?php

namespace Tomahawk\HttpKernel\Tests;

use Tomahawk\HttpKernel\Config\FileLocator;
use PHPUnit\Framework\TestCase;

class FileLocatorTest extends TestCase
{
    /**
     * @expectedException \LogicException
     */
    public function testLocate()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\KernelInterface')->getMock();
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
        $locator->locate('/some/path');
    }

    public function testLocateWithGlobalResourcePath()
    {
        $kernel = $this->getMockBuilder('Tomahawk\HttpKernel\KernelInterface')->getMock();
        $kernel
            ->expects($this->atLeastOnce())
            ->method('locateResource')
            ->with('@BundleName/some/path', '/global/resource/path', false);

        $locator = new FileLocator($kernel, '/global/resource/path');
        $locator->locate('@BundleName/some/path', null, false);
    }
}
