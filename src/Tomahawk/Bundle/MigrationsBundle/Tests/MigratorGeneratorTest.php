<?php

namespace Tomahawk\Bundle\MigrationsBundle\Tests;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\MigrationsBundle\Migrator\MigrationGenerator;
use Symfony\Component\Finder\Finder;

class MigratorGeneratorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;
    protected $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/tomahawk';
        $this->filesystem = new Filesystem();
        //$this->filesystem->mkdir($this->tmpDir.'/Migration');
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->tmpDir);
    }

    public function testGenerate()
    {
        $generator = $this->getGenerator();

        $migration = sprintf('M%dMigration', time());

        $file = 'Migration/'.$migration .'.php';

        $generator->generate($this->getBundle(), $migration);

        $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $migration));

        $content = file_get_contents($this->tmpDir.'/'.$file);
        $strings = array(
            'namespace Foo\\BarBundle\\Migration',
            'class ' . $migration,
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateModelFileExists()
    {
        $migration = sprintf('M%dMigration', time());

        $this->filesystem->mkdir($this->tmpDir.'/Migration');
        $this->filesystem->touch($this->tmpDir.'/Migration/'.$migration.'.php');

        $this->getGenerator()->generate($this->getBundle(), $migration);

    }

    /**
     * @return BundleInterface
     */
    protected function getBundle()
    {
        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue('FooBarBundle'));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));

        return $bundle;
    }


    /**
     * @return MigrationGenerator
     */
    protected function getGenerator()
    {
        $generator = new MigrationGenerator($this->filesystem);
        $generator->setSkeletonDirs(__DIR__.'/../Resources/skeleton');

        return $generator;
    }
}