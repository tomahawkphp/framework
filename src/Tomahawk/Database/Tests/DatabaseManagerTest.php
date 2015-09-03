<?php

namespace Tomahawk\Database\Tests;

use Tomahawk\Test\TestCase;
use Tomahawk\Database\DatabaseManager;

class DatabaseManagerTest extends TestCase
{
    public function testManager()
    {
        $resolver = $this->getMock('Illuminate\Database\ConnectionResolverInterface');

        $connection = $this->getMockBuilder('Illuminate\Database\Connection')->disableOriginalConstructor()->getMock();
        $builder = $this->getMockBuilder('Illuminate\Database\Query\Builder')->disableOriginalConstructor()->getMock();
        $schema = $this->getMockBuilder('Illuminate\Database\Schema\Builder')->disableOriginalConstructor()->getMock();

        $manager = new DatabaseManager($resolver);

        $resolver->expects($this->any())->method('connection')->willReturn($connection);
        $connection->expects($this->any())->method('table')->willReturn($builder);

        $resolver->expects($this->any())->method('connection')->willReturn($connection);
        $connection->expects($this->any())->method('getSchemaBuilder')->willReturn($schema);

        $manager->connection();
        $manager->table('table');
        $manager->schema();
    }
}
