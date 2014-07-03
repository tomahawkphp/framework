<?php

use Tomahawk\Database\DatabaseManager;

class DatabaseManagerTest extends PHPUnit_Framework_TestCase
{
    public function testManager()
    {
        $resolver = Mockery::mock('Illuminate\Database\ConnectionResolverInterface');

        $connection = Mockery::mock('Illuminate\Database\Connection');
        $builder = Mockery::mock('Illuminate\Database\Query\Builder');
        $schema = Mockery::mock('Illuminate\Database\Schema\Builder');

        $manager = new DatabaseManager($resolver);

        $resolver->shouldReceive('connection')->ordered()->andReturn($connection);
        $connection->shouldReceive('table')->andReturn($builder);

        $resolver->shouldReceive('connection')->ordered()->andReturn($connection);
        $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);

        $manager->connection();
        $manager->table('table');
        $manager->schema();
    }
}