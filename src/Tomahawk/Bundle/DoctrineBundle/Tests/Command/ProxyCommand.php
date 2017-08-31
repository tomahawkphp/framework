<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Tomahawk\Console\Application;
use Tomahawk\HttpKernel\TestKernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ProxyCommand extends TestCase
{

    /**
     * @param \Symfony\Component\Console\Command\Command $command
     * @return CommandTester
     */
    protected function getCommandTester(Command $command)
    {
        $app = new TestKernel('prod', false);
        $app->boot();
        $application = new Application($app);
        $application->setAutoExit(false);


        $container = $application->getKernel()->getContainer();

        $container->set('doctrine', $this->getDoctrineMock());

        $application->add($command);

        $command = $application->find($command->getName());
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    protected function getDoctrineMock()
    {
        $entityManager = $this->getEntityManagerMock();

        $registry = $this->getMockBuilder('Tomahawk\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $registry->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($entityManager));

        $registry->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->getConnectionMock()));

        return $registry;
    }

    protected function getEntityManagerMock()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->getConnectionMock()));

        $entityManager->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($this->getConfigMock()));

        $metaDataFactory = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataFactory')->getMock();

        $metaDataFactory->expects($this->any())
            ->method('getAllMetadata')
            ->will($this->returnValue(null));

        $entityManager->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metaDataFactory));

        return $entityManager;
    }

    protected function getConnectionMock()
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        return $connection;
    }

    protected function getConfigMock()
    {
        $config = $this->getMockBuilder('Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->any())
            ->method('getMetadataCacheImpl')
            ->will($this->returnValue(null));

        $config->expects($this->any())
            ->method('ensureProductionSettings')
            ->will($this->throwException(new \Exception()));

        return $config;
    }
}
