<?php

namespace Tomahawk\Bundle\DoctrineBundle\Tests;

use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\ClearResultCacheCommand;
use Tomahawk\Bundle\DoctrineBundle\Tests\ProxyCommand;

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