<?php

namespace Tomahawk\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Tomahawk\Filesystem\Filesystem;

class FilesystemTest extends TestCase
{
    /**
     * @var
     */
    private $tempDir;

    public function setUp()
    {
        $this->tempDir = __DIR__.'/tmp';
        mkdir($this->tempDir);
    }

    public function tearDown()
    {
        $files = new Filesystem();
        $files->remove($this->tempDir);
    }

    public function testRequireOnceRequiresFileProperly()
    {
        $filesystem = new Filesystem();

        mkdir($this->tempDir.'/foo');
        file_put_contents($this->tempDir.'/foo/foo.php', '<?php function random_function_xyz(){};');
        $filesystem->requireOnce($this->tempDir.'/foo/foo.php');

        file_put_contents($this->tempDir.'/foo/foo.php', '<?php function random_function_xyz_changed(){};');

        $filesystem->requireOnce($this->tempDir.'/foo/foo.php');

        $this->assertTrue(function_exists('random_function_xyz'));
        $this->assertFalse(function_exists('random_function_xyz_changed'));
    }

    public function testGlobFindsFiles()
    {
        file_put_contents($this->tempDir.'/foo.txt', 'foo');
        file_put_contents($this->tempDir.'/bar.txt', 'bar');
        $files = new Filesystem;
        $glob = $files->glob($this->tempDir.'/*.txt');
        $this->assertContains($this->tempDir.'/foo.txt', $glob);
        $this->assertContains($this->tempDir.'/bar.txt', $glob);
    }
}
