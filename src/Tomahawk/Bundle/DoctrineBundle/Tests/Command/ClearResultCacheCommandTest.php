<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests\Command;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ClearResultCacheCommand;

class ClearResultCacheCommandTest extends ProxyCommand
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCommand()
    {
        $command = new ClearResultCacheCommand();
        $commandTester = $this->getCommandTester($command);

        $commandTester->execute(array('command' => $command->getName()));
    }
}