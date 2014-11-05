<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ClearMetadataCacheCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

class ClearMetadataCacheCommandTest extends ProxyCommand
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCommand()
    {
        $command = new ClearMetadataCacheCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}