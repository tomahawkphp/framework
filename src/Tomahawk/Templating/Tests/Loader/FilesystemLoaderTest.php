<?php

use Tomahawk\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Config\FileLocatorInterface;
use Tomahawk\Templating\TemplateReference;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadReturnFalseOnFailure()
    {
        $reference = new TemplateReference(null, null, 'index', 'php');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface', array('locate'));
        $locator->expects($this->any())
            ->method('locate')
            ->will($this->throwException(new InvalidArgumentException()));

        $filesystemLoader = new FilesystemLoader($locator);

        $this->assertFalse($filesystemLoader->load($reference));
    }

    public function testLoad()
    {
        $reference = new TemplateReference(null, null, 'index', 'php');
        $fileStorage = new FileStorage(__DIR__ .'/Resources/index.php');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface', array('locate'));
        $locator->expects($this->any())
            ->method('locate')
            ->will($this->returnValue($fileStorage));


        $filesystemLoader = new FilesystemLoader($locator);
        $storage = $filesystemLoader->load($reference);

        $this->assertInstanceOf('Symfony\Component\Templating\Storage\Storage', $storage);
    }

    public function testFreshReturnFalseOnFailure()
    {
        $reference = new TemplateReference(null, null, 'index', 'php');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface', array('locate'));
        $locator->expects($this->any())
            ->method('locate')
            ->will($this->throwException(new InvalidArgumentException()));

        $filesystemLoader = new FilesystemLoader($locator);

        $this->assertFalse($filesystemLoader->isFresh($reference, time() + 600));
    }

    public function testFreshNotReadableReturnsFalse()
    {
        $reference = new TemplateReference(null, null, 'foo', 'php');
        $fileStorage = new FileStorage(__DIR__ .'/Resources/foo.php');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface', array('locate'));
        $locator->expects($this->any())
            ->method('locate')
            ->will($this->returnValue($fileStorage));

        $filesystemLoader = new FilesystemLoader($locator);

        $this->assertFalse($filesystemLoader->isFresh($reference, time() + 600));
    }

    public function testIsFresh()
    {
        $filesystem = new Filesystem();
        $filesystem->touch(__DIR__ .'/Resources/index.php');

        $reference = new TemplateReference(null, null, 'index', 'php');
        $fileStorage = new FileStorage(__DIR__ .'/Resources/index.php');

        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface', array('locate'));
        $locator->expects($this->any())
            ->method('locate')
            ->will($this->returnValue($fileStorage));

        $filesystemLoader = new FilesystemLoader($locator);

        $this->assertTrue($filesystemLoader->isFresh($reference, time() + 3600));
    }
}
