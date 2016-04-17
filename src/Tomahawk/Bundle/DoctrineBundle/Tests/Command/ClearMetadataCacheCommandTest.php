<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ClearMetadataCacheCommand;

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