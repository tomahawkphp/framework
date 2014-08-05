<?php

namespace Tomahawk\Bundle\MigrationsBundle\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Bundle\MigrationsBundle\Migration\MigrationRepo;

class MigratorRepoTest extends TestCase
{
    public function testRepositoryExists()
    {
        $migratorRepo = $this->getMigratorRepo();

        $this->assertTrue($migratorRepo->repositoryExists());
    }

    public function testRepositoryReturnsCorrectTableName()
    {
        $migratorRepo = $this->getMigratorRepo();

        $this->assertEquals('tomahawk_migrations', $migratorRepo->getTableName());
    }

    public function testGetConnectionResolver()
    {
        $migratorRepo = $this->getMigratorRepo();
        $this->assertInstanceOf('Illuminate\Database\ConnectionResolverInterface', $migratorRepo->getConnectionResolver());
    }

    public function testCreateRepository()
    {
        $blueprint = $this->getMockBuilder('Illuminate\Database\Schema\Blueprint')
            ->setMethods(array(
                'string',
                'integer',
                'build'
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $blueprint->expects($this->exactly(2))
            ->method('string');

        $blueprint->expects($this->once())
            ->method('integer');

        $blueprint->expects($this->once())
            ->method('build');

        $migratorRepo = $this->getMigratorRepoForCreate();

        $this->assertTrue($migratorRepo->createRepository($blueprint));

    }

    public function testCreateRepositoryThrowsException()
    {
        $blueprint = $this->getMockBuilder('Illuminate\Database\Schema\Blueprint')
            ->setMethods(array(
                'string',
                'integer',
                'build'
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $blueprint->expects($this->exactly(2))
            ->method('string');

        $blueprint->expects($this->once())
            ->method('integer');

        $blueprint->expects($this->once())
            ->method('build')
            ->will($this->throwException(new \Exception('exception')));

        $migratorRepo = $this->getMigratorRepoForCreate();

        $this->assertFalse($migratorRepo->createRepository($blueprint));

    }

    public function testGetRan()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->expects($this->once())
            ->method('lists');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->once())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $migratorRepo->getRan();
    }

    public function testLog()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->expects($this->once())
            ->method('insert');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->once())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $migratorRepo->log('foo', 99);
    }

    public function testDelete()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('delete');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->once())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $migration = new \stdClass();
        $migration->migration = 'foo';

        $migratorRepo->delete($migration);
    }

    public function testGetBatchNumber()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->expects($this->exactly(2))
            ->method('max')
            ->will($this->returnValue(99));

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->exactly(2))
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $this->assertEquals(99, $migratorRepo->getLastBatchNumber());

        $this->assertEquals(100, $migratorRepo->getNextBatchNumber());

    }

    public function testGetLast()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('get');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->exactly(2))
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $migratorRepo->getLast();
    }

    public function testGetAll()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('get');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'table'
            ))
            ->getMock();

        $connection->expects($this->once())
            ->method('table')
            ->will($this->returnValue($queryBuilder));

        $controllerResolver = $this->getConnectionResolver($connection);

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');

        $migratorRepo->getAll();
    }

    /**
     * @return MigrationRepo
     */
    protected function getMigratorRepo()
    {
        $connection = $this->getConnection();

        $controllerResolver = $this->getMock('Illuminate\Database\ConnectionResolverInterface');

        $controllerResolver
            ->expects($this->any())
            ->method('connection')
            ->will($this->returnValue($connection));

        $migratorRepo = new MigrationRepo($controllerResolver, 'tomahawk_migrations');


        return $migratorRepo;
    }

    protected function getConnection()
    {
        $queryBuilder = $this->getQueryBuilder();

        $schemaBuilder = $this->getMockBuilder('Illuminate\Database\Schema\Builder')
            ->setMethods( array(
                'hasTable',
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $schemaBuilder->expects($this->any())
            ->method('hasTable')
            ->will($this->returnValue(true));

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->setMethods( array(
                'getSchemaBuilder',
                'table'
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getSchemaBuilder')
            ->will($this->returnValue($schemaBuilder));

        $connection->expects($this->any())
            ->method('table')
            ->will($this->returnValue($queryBuilder));


        return $connection;
    }

    protected function getMigratorRepoForCreate()
    {
        $schemaGrammar = $this->getMockBuilder('Illuminate\Database\Schema\Grammars\Grammar')
            ->disableOriginalConstructor()
            ->getMock();


        $connection = $this->getMockBuilder('Illuminate\Database\Connection')
            ->setMethods( array(
                'getSchemaGrammar'
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getSchemaGrammar')
            ->will($this->returnValue($schemaGrammar));

        $controllerResolver = $this->getMock('Illuminate\Database\ConnectionResolverInterface');

        $controllerResolver
            ->expects($this->any())
            ->method('connection')
            ->will($this->returnValue($connection));

        $migratorRepo = new MigrationRepo($controllerResolver, 'migrations');


        return $migratorRepo;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getQueryBuilder()
    {
        $queryBuilder = $this->getMockBuilder('Illuminate\Database\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods( array(
                'insert',
                'orderBy',
                'where',
                'delete',
                'get',
                'lists',
                'max',
            ))
            ->getMock();

        return $queryBuilder;
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
}