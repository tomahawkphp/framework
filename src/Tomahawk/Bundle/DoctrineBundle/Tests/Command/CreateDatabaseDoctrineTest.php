<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tomahawk\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;

class CreateDatabaseDoctrineTest extends ProxyCommand
{
    public function testExecute()
    {
        $connectionName = 'default';
        $dbName = 'test';
        $params = array(
            'dbname' => $dbName,
            'memory' => true,
            'driver' => 'pdo_sqlite',
        );
        $application = new Application();
        $application->add(new CreateDatabaseDoctrineCommand());
        $command = $application->find('doctrine:database:create');
        $command->setContainer($this->getMockContainer($connectionName, $params));
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array_merge(array('command' => $command->getName()))
        );
        $this->assertContains("Created database \"$dbName\" for connection named $connectionName", $commandTester->getDisplay());
    }
    public function testExecuteWithShardOption()
    {
        $connectionName = 'default';
        $params = array(
            'dbname' => 'test',
            'memory' => true,
            'driver' => 'pdo_sqlite',
            'global' => array(
                'driver' => 'pdo_sqlite',
                'dbname' => 'test',
            ),
            'shards' => array(
                'foo' => array(
                    'id' => 1,
                    'dbname' => 'shard_1',
                    'driver' => 'pdo_sqlite',
                )
            )
        );
        $application = new Application();
        $application->add(new CreateDatabaseDoctrineCommand());
        $command = $application->find('doctrine:database:create');
        $command->setContainer($this->getMockContainer($connectionName, $params));
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--shard' => 1));
        $this->assertContains("Created database \"shard_1\" for connection named $connectionName", $commandTester->getDisplay());
    }
    /**
     * @param string     $connectionName Connection name
     * @param array|null $params         Connection parameters
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockContainer($connectionName, $params = null)
    {
        // Mock the container and everything you'll need here
        $mockDoctrine = $this->getMockBuilder('Doctrine\Common\Persistence\ConnectionRegistry')
            ->getMock();
        $mockDoctrine->expects($this->any())
            ->method('getDefaultConnectionName')
            ->withAnyParameters()
            ->willReturn($connectionName);
        $mockConnection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(array('getParams'))
            ->getMockForAbstractClass();
        $mockConnection->expects($this->any())
            ->method('getParams')
            ->withAnyParameters()
            ->willReturn($params);
        $mockDoctrine->expects($this->any())
            ->method('getConnection')
            ->withAnyParameters()
            ->willReturn($mockConnection);
        $mockContainer = $this->getMockBuilder('Tomahawk\DependencyInjection\Container')
            ->setMethods(array('get'))
            ->getMock();
        $mockContainer->expects($this->any())
            ->method('get')
            ->with('doctrine')
            ->willReturn($mockDoctrine);
        return $mockContainer;
    }
}