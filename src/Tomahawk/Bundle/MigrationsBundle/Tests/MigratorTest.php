<?php

namespace Tomahawk\Bundle\MigrationsBundle\Tests;

use Symfony\Component\HttpKernel\Kernel;
use Tomahawk\Bundle\MigrationsBundle\Migration\MigrationGenerator;
use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Tomahawk\Bundle\MigrationsBundle\Migration\Migrator;
use Symfony\Component\Finder\Finder;

class MigratorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;
    protected $tmpDir;
    protected $migrationFile;
    protected $migrationPath;
    protected $migrationClass;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/tomahawk';
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->tmpDir.'/Migration');

        $this->migrationPath = __DIR__ .'/../Test/';
        $this->migrationFile = realpath(__DIR__ .'/../Test/Migration/M1406873315Migration.php');
        $this->migrationClass = 'M1406873315Migration';
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->tmpDir);
    }

    public function testNoMigrationsToRun()
    {
        $bundles = array($this->getBundle());

        $kernel = $this->getKernel(array(), $bundles);

        $migrator = new Migrator($this->getMigratorRepo(), new Finder(), $kernel);

        $migrator->run();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);

        $this->assertEquals('<info>Nothing to migrate.</info>', $notes[0]);
    }


    public function testMigrateUp()
    {
        $bundles = array($this->getBundle('MigrationsBundle', 'Tomahawk\Bundle\MigrationsBundle\Test', $this->migrationPath));

        $kernel = $this->getKernel(array(), $bundles);

        $finder = $this->getFinder(array($this->getSplFileInfo()));

        $migrator = new Migrator($this->getMigratorRepo(), $finder, $kernel);

        $migrator->run();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);
        $this->assertRegExp('/<info>Migrated:<\/info>/', $notes[0]);
    }

    public function testMigrateDown()
    {
        $migration = new \stdClass();
        $migration->bundle = 'MigrationsBundle';
        $migration->migration = $this->migrationClass;
        $migration->batch = 1;

        $bundles = array($this->getBundle('MigrationsBundle', 'Tomahawk\Bundle\MigrationsBundle\Test', $this->migrationPath));

        $kernel = $this->getKernel(array(), $bundles);

        $finder = $this->getFinder(array($this->getSplFileInfo()));

        $migrator = new Migrator($this->getMigratorRepo(array(), array($migration)), $finder, $kernel);

        $migrator->rollback();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);
        $this->assertRegExp('/<info>Rolled back:<\/info>/', $notes[0]);
    }

    public function testMigrateDownHasNothingToRollback()
    {
        $migration = new \stdClass();
        $migration->bundle = 'MigrationsBundle';
        $migration->migration = $this->migrationClass;
        $migration->batch = 1;

        $bundles = array($this->getBundle('MigrationsBundle', 'Tomahawk\Bundle\MigrationsBundle\Test', $this->migrationPath));

        $kernel = $this->getKernel(array(), $bundles);

        $finder = $this->getFinder(array($this->getSplFileInfo()));

        $migrator = new Migrator($this->getMigratorRepo(), $finder, $kernel);

        $migrator->rollback();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);
        $this->assertEquals('<info>Nothing to rollback.</info>', $notes[0]);
    }

    public function testMigrateReset()
    {
        $migration = new \stdClass();
        $migration->bundle = 'MigrationsBundle';
        $migration->migration = $this->migrationClass;
        $migration->batch = 1;

        $bundles = array($this->getBundle('MigrationsBundle', 'Tomahawk\Bundle\MigrationsBundle\Test', $this->migrationPath));

        $kernel = $this->getKernel(array(), $bundles);

        $finder = $this->getFinder(array($this->getSplFileInfo()));

        $migrator = new Migrator($this->getMigratorRepo(array(), array(), array($migration)), $finder, $kernel);

        $migrator->reset();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);
        $this->assertRegExp('/<info>Rolled back:<\/info>/', $notes[0]);
    }

    public function testMigrateResetNothingToReset()
    {
        $migration = new \stdClass();
        $migration->bundle = 'MigrationsBundle';
        $migration->migration = $this->migrationClass;
        $migration->batch = 1;

        $bundles = array($this->getBundle('MigrationsBundle', 'Tomahawk\Bundle\MigrationsBundle\Test', $this->migrationPath));

        $kernel = $this->getKernel(array(), $bundles);

        $finder = $this->getFinder(array($this->getSplFileInfo()));

        $migrator = new Migrator($this->getMigratorRepo(), $finder, $kernel);

        $migrator->reset();

        $notes = $migrator->getNotes();
        $this->assertCount(1, $notes);
        $this->assertEquals('<info>Nothing to rollback.</info>', $notes[0]);
    }

    protected function getMigratorRepo($ran = array(), $last = array(), $all = array())
    {
        $connection = $this->getConnection();

        $connectionResolver = $this->getConnectionResolver($connection);

        $migratorRepo = $this->getMockBuilder('Tomahawk\Bundle\MigrationsBundle\Migration\MigrationRepo')
            ->disableOriginalConstructor()
            ->getMock();

        $migratorRepo->expects($this->any())
            ->method('getRan')
            ->will($this->returnValue($ran));

        $migratorRepo->expects($this->any())
            ->method('getLast')
            ->will($this->returnValue($last));

        $migratorRepo->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($all));

        $migratorRepo->expects($this->any())
            ->method('getConnectionResolver')
            ->will($this->returnValue($connectionResolver));

        return $migratorRepo;
    }

    protected function getMigratorRepoForDown($return)
    {
        $connection = $this->getConnection();

        $connectionResolver = $this->getConnectionResolver($connection);

        $migratorRepo = $this->getMockBuilder('Tomahawk\Bundle\MigrationsBundle\Migration\MigrationRepo')
            ->disableOriginalConstructor()
            ->getMock();

        $migration = new \stdClass();
        $migration->bundle = 'FooBarBundle';
        $migration->migration = $this->ranMigration;
        $migration->batch = 1;

        $migratorRepo->expects($this->once())
            ->method('getLast')
            ->will($this->returnValue(array($migration)));


        $migratorRepo->expects($this->any())
            ->method('getConnectionResolver')
            ->will($this->returnValue($connectionResolver));

        return $migratorRepo;
    }

    /**
     * @param string $name
     * @param string $namespace
     * @param null $path
     * @return BundleInterface
     */
    protected function getBundle($name = 'FooBarBundle', $namespace = 'Foo\BarBundle', $path = null)
    {
        if (null === $path) {
            $path = $this->tmpDir;
        }

        $bundle = $this->getMock('Tomahawk\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($path));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue($name));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));

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

    public function getSplFileInfo()
    {
        $file = \Mockery::mock('stdClass');
        $file->shouldReceive('getRealPath')->once()->andReturn($this->migrationFile);

        return $file;
    }

    protected function getConnection()
    {
        $schemaBuilder = $this->getMockBuilder('Illuminate\Database\Schema\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getSchemaBuilder')
            ->will($this->returnValue($schemaBuilder));

        return $connection;
    }

    /**
     * @param $connection
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getConnectionResolver($connection)
    {
        $controllerResolver = $this->getMock('Illuminate\Database\ConnectionResolverInterface');

        $controllerResolver
            ->expects($this->any())
            ->method('connection')
            ->will($this->returnValue($connection));

        return $controllerResolver;
    }

    /**
     * Returns a mock for the abstract kernel.
     *
     * @param array $methods Additional methods to mock (besides the abstract ones)
     * @param array $bundles Bundles to register
     *
     * @return Kernel
     */
    protected function getKernel(array $methods = array(), array $bundles = array())
    {
        $methods[] = 'getBundles';
        $methods[] = 'getBundle';

        $kernel = $this
            ->getMockBuilder('Tomahawk\HttpKernel\Kernel')
            ->setMethods($methods)
            ->setConstructorArgs(array('test', false))
            ->getMockForAbstractClass();

        $kernel->expects($this->any())
            ->method('getBundle')
            ->will($this->returnValue($bundles[0]));

        $kernel->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue($bundles));

        return $kernel;
    }

    /**
     * @param array $return
     * @return Finder
     */
    protected function getFinder($return = array())
    {
        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        $finder->expects($this->any())
            ->method('files')
            ->will($this->returnSelf());

        $finder->expects($this->any())
            ->method('in')
            ->will($this->returnSelf());

        $finder->expects($this->any())
            ->method('name')
            ->will($this->returnSelf());

        $finder->expects($this->any())
            ->method('sortByName')
            ->will($this->returnValue($return));

        return $finder;
    }
}